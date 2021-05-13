<?php /** @noinspection PhpMissingReturnTypeInspection */

namespace app\components\diff\amendmentMerger;

use app\components\diff\{DataTypes\DiffWord, DataTypes\GroupedParagraphData, DataTypes\ParagraphMergerWord, Diff, DiffRenderer};
use app\components\UrlHelper;
use app\models\db\Amendment;
use yii\helpers\Html;

class ParagraphMerger
{
    /** @var ParagraphOriginalData */
    private $paraData;

    /** @var ParagraphDiff[] */
    private $diffs;

    private $merged = false;

    public function __construct(string $paragraphStr)
    {
        $origTokenized = Diff::tokenizeLine($paragraphStr);
        $words         = [];
        foreach ($origTokenized as $x) {
            $word = new ParagraphMergerWord();
            $word->orig = $x;
            $words[] = $word;
        }
        $this->paraData = new ParagraphOriginalData($paragraphStr, $origTokenized, $words);
        $this->diffs    = [];
    }

    /**
     * @param int $amendmentId
     * @param DiffWord[] $wordArr
     */
    public function addAmendmentParagraph($amendmentId, $wordArr)
    {
        $hasChanges = false;
        $firstDiff  = null;
        for ($i = 0; $i < count($wordArr); $i++) {
            if ($wordArr[$i]->amendmentId !== null) {
                $hasChanges = true;
                if ($firstDiff === null) {
                    $firstDiff = $i;
                }
            }
        }

        if ($hasChanges) {
            $this->diffs[] = new ParagraphDiff($amendmentId, $firstDiff, $wordArr);
        }
    }

    /*
     * Sort the amendment paragraphs by the first affected line/word descendingly.
     * This is an attempt to minimize the number of collisions when merging the paragraphs later on,
     * as amendments changing a lot and therefore colliding more frequently tend to start at earlier lines.
     */
    private function sortDiffParagraphsFromLastToFirst(): void
    {
        usort($this->diffs, function (ParagraphDiff $val1, ParagraphDiff $val2) {
            return $val2->firstDiff <=> $val1->firstDiff;
        });
    }

    private function sortCollisionsFromFirstToLast(): void
    {
        usort($this->paraData->collidingParagraphs, function (CollidingParagraphDiff $para1, CollidingParagraphDiff $para2) {
            return $para1->firstDiff <=> $para2->firstDiff;
        });
    }

    private function moveInsertIntoOwnWord(int $amendingNo, int $wordNo, string $insert): void
    {
        $insertArr = function ($arr, $pos, $insertedEl) {
            return array_merge(array_slice($arr, 0, $pos + 1), [$insertedEl], array_slice($arr, $pos + 1));
        };

        // Figures out if the blank element is to be inserted in the middle of a deletion block.
        // If so, the "amendmentId"-Attribute needs to be set to trigger a collision
        $pendingDeleteAmendment = function ($locAmendNo, $wordNo) {
            if ($wordNo == 0) {
                return null;
            }

            while ($wordNo >= 0) {
                $str = explode("###DEL_", $this->diffs[$locAmendNo]->diff[$wordNo]->diff);
                if (count($str) > 1 && strpos($str[count($str) - 1], 'START') === 0) {
                    return $this->diffs[$locAmendNo]->diff[$wordNo]->amendmentId;
                }
                if (count($str) > 1 && strpos($str[count($str) - 1], 'END') === 0) {
                    return null;
                }
                $wordNo--;
            }

            return null;
        };

        $this->paraData->origTokenized = $insertArr($this->paraData->origTokenized, $wordNo, '');
        $this->paraData->words         = $insertArr($this->paraData->words, $wordNo, new ParagraphMergerWord());

        foreach ($this->diffs as $locAmendNo => $changeSet) {
            if ($locAmendNo == $amendingNo) {
                $amendmentId                    = $changeSet->diff[$wordNo]->amendmentId;
                $changeSet->diff[$wordNo]->diff = $changeSet->diff[$wordNo]->word;
                $changeSet->diff[$wordNo]->amendmentId = null;

                $toInsert = new DiffWord();
                $toInsert->diff = $insert;
                $toInsert->amendmentId = $amendmentId;
                $changeSet->diff = $insertArr($changeSet->diff, $wordNo, $toInsert);
            } else {
                $insertArrEl = new DiffWord();
                $preAm       = $pendingDeleteAmendment($locAmendNo, $wordNo);
                if ($preAm !== null) {
                    $insertArrEl->amendmentId = $preAm;
                }
                $changeSet->diff = $insertArr($changeSet->diff, $wordNo, $insertArrEl);
            }
            $this->diffs[$locAmendNo] = $changeSet;
        }
    }

    /*
     * Inserting new words / paragraphs is stored like "</p>###INS_START###...###INS_END###,
     * being assigned to the "</p>" token. This makes multiple insertions after </p> colliding with each other.
     * This workaround splits this up by inserting empty tokens in the original word array
     * and moving the insertion to this newly created index.
     * To maintain consistency, we need to insert the new token both in the original word array as well as in _all_
     * amendments affecting this paragraph.
     *
     * This isn't exactly very elegant, as the data structure mutates as we're iterating over it,
     * therefore we need to cancel out the side-effects.
     *
     * AmendmentRewriter::moveInsertsIntoTheirOwnWords does about the same and should behave similarily
     */
    private function moveInsertsIntoTheirOwnWords(): void
    {
        foreach ($this->diffs as $changeSetNo => $changeSet) {
            $changeSet = $this->diffs[$changeSetNo];
            $words     = count($changeSet->diff);
            for ($wordNo = 0; $wordNo < $words; $wordNo++) {
                $word  = $changeSet->diff[$wordNo];
                $split = explode('###INS_START###', $word->diff);
                if (count($split) === 2 && $split[0] === $word->word) {
                    $this->moveInsertIntoOwnWord($changeSetNo, $wordNo, '###INS_START###' . $split[1]);
                    $changeSet = $this->diffs[$changeSetNo];
                    $wordNo++;
                    $words++;
                }
            }
        }
    }


    /**
     * Identify adjacent tokens that are about to be changed and check if any of the changes leads to a collision.
     *
     * @return ParagraphDiffGroup[]
     */
    private function groupChangeSet(ParagraphDiff $changeSet): array
    {
        /** @var ParagraphDiffGroup[] $foundGroups */
        $foundGroups = [];

        /** @var DiffWord[]|null $currTokens */
        $currTokens = null;
        $currGroupCollides = null;
        $currGroupFirstCollision = null;
        $currGroupLastCollision = null;

        /** @var int[] $currCollisionIds */
        $currCollisionIds = [];

        foreach ($changeSet->diff as $i => $token) {
            if ($token->amendmentId !== null) {
                if ($currTokens === null) {
                    $currGroupCollides = false;
                    $currCollisionIds = [];
                    $currTokens = [];
                    $currGroupFirstCollision = null;
                    $currGroupLastCollision = null;
                }
                $currTokens[$i] = $token;
                if ($this->paraData->words[$i]->modifiedBy > 0) {
                    $currGroupCollides = true;
                    if (!in_array($this->paraData->words[$i]->modifiedBy, $currCollisionIds)) {
                        $currCollisionIds[] = $this->paraData->words[$i]->modifiedBy;
                    }
                    if ($currGroupFirstCollision === null) {
                        $currGroupFirstCollision = $i;
                    }
                    $currGroupLastCollision = $i;
                }
            } else {
                if ($currTokens !== null) {
                    $foundGroup = new ParagraphDiffGroup();
                    $foundGroup->tokens = $currTokens;
                    $foundGroup->collides = $currGroupCollides;
                    $foundGroup->collisionIds = $currCollisionIds;
                    $foundGroup->firstCollisionPos = $currGroupFirstCollision;
                    $foundGroup->lastCollisionPos = $currGroupLastCollision;
                    $foundGroups[] = $foundGroup;

                    $currTokens = null;
                    $currGroupCollides = null;
                    $currCollisionIds = [];
                }
            }
        }
        if ($currTokens !== null) {
            $foundGroup = new ParagraphDiffGroup();
            $foundGroup->tokens = $currTokens;
            $foundGroup->collides = $currGroupCollides;
            $foundGroup->collisionIds = $currCollisionIds;
            $foundGroup->firstCollisionPos = $currGroupFirstCollision;
            $foundGroup->lastCollisionPos = $currGroupLastCollision;
            $foundGroups[] = $foundGroup;
        }

        return $foundGroups;
    }

    private function mergeParagraphRegularily(ParagraphDiff $changeSet): void
    {
        $words = $this->paraData->words;

        $paragraphHadCollisions = false;
        $collisionIds = [];
        $collidingGroups = [];

        $groups = $this->groupChangeSet($changeSet);
        foreach ($groups as $group) {
            // Transfer the diff from the non-colliding groups to the merged diff and remove it from the changeset.
            // The changeset that remains will contain the un-mergable collisions

            if ($group->collides) {
                $paragraphHadCollisions = true;
                $collisionIds = array_merge($collisionIds, $group->collisionIds);
                $collidingGroups[] = $group;
                continue;
            }

            foreach ($group->tokens as $i => $token) {
                // Apply the changes to the paragraph
                $words[$i]->modification = $token->diff;
                $words[$i]->modifiedBy = $token->amendmentId;

                // Only the colliding changes are left in the changeset
                $changeSet->diff[$i]->amendmentId = null;
                $changeSet->diff[$i]->diff = $changeSet->diff[$i]->word;
            }
        }

        $this->paraData->words = $words;
        if ($paragraphHadCollisions) {
            $collisionIds = array_unique($collisionIds);
            $this->paraData->collidingParagraphs[] = new CollidingParagraphDiff(
                $changeSet->amendment,
                $changeSet->firstDiff,
                $changeSet->diff,
                $collisionIds,
                $collidingGroups
            );
        }
    }

    /*
     * The diff group should be saved into the appendCollisionGroups property of the last word of the first changeset that it collides with.
     * (An alternative would be to store it to the last word of the last changeset it collides with)
     */
    private function mergeCollidedParagraphGroup(ParagraphDiffGroup $paragraphDiffGroup): void {
        $affectedAmendmentId = $this->paraData->words[$paragraphDiffGroup->firstCollisionPos]->modifiedBy;
        $merged = false;
        for ($i = $paragraphDiffGroup->firstCollisionPos; $i < count($this->paraData->words) && !$merged; $i++) {
            if ($i === count($this->paraData->words) - 1 || $this->paraData->words[$i + 1]->modifiedBy !== $affectedAmendmentId) {
                if ($this->paraData->words[$i]->appendCollisionGroups === null) {
                    $this->paraData->words[$i]->appendCollisionGroups = [];
                }
                $this->paraData->words[$i]->appendCollisionGroups[] = $paragraphDiffGroup;
                $merged = true;
            }
        }
    }

    private function tryMergingCollidedParagraph(CollidingParagraphDiff $paragraph): bool {
        // @TODO
        // Check if it's safe and senseful to merge it. E.g.: no HTML tags contained, deleted part is not too long

        foreach ($paragraph->collidingGroups as $collidingGroup) {
            $this->mergeCollidedParagraphGroup($collidingGroup);
        }

        /*
        echo '<pre>';
        var_dump($paragraph->collidingGroups);
        var_dump($this->paraData->words);
        echo '</pre>';
        */

        return true;
    }

    private function merge(): void
    {
        if ($this->merged) {
            return;
        }

        $this->sortDiffParagraphsFromLastToFirst();
        $this->moveInsertsIntoTheirOwnWords();

        //echo "======== ORIGINAL DIFFS ========\n";
        //var_dump($this->diffs);

        foreach ($this->diffs as $changeSet) {
            $this->mergeParagraphRegularily($changeSet);
        }

        //echo "======== REGULARILY MERGED WORDS ========\n";
        //var_dump($this->paraData->words);
        //echo "======== COLLIDING PARAGRAPHS ========\n";
        //var_dump($this->paraData->collidingParagraphs);

        $this->sortCollisionsFromFirstToLast();

        $this->paraData->collidingParagraphs = array_values(array_filter(
            $this->paraData->collidingParagraphs,
            function (CollidingParagraphDiff $collidingParagraphDiff) {
                return !$this->tryMergingCollidedParagraph($collidingParagraphDiff);
            }
        ));

        //echo "======== MERGED ========\n";
        //var_dump($this->paraData->words);

        $this->merged = true;
    }

    /**
     * @param ParagraphMergerWord[] $words
     *
     * @return GroupedParagraphData[]
     */
    public static function groupParagraphData(array $words, ?int &$CHANGESET_COUNTER = null): array
    {
        /** @var GroupedParagraphData[] $groupedParaData */
        $groupedParaData  = [];
        $pending          = '';
        $pendingCurrAmend = 0;

        foreach ($words as $word) {
            if ($word->modifiedBy !== null) {
                if ($pendingCurrAmend === 0 && !in_array($word->orig, ['', '#', '##', '###'])) { // # would lead to conflicty with ###DEL_START### in the modification
                    if (mb_strpos($word->modification, $word->orig) === 0) {
                        // The current word has an unchanged beginning + an insertion or deletion
                        // => the unchanged part will be added to the $pending queue (which will be added to $groupedParaData in the next "if" statement
                        $shortened            = mb_substr($word->modification, mb_strlen($word->orig));
                        $pending              .= $word->orig;
                        $word->modification = $shortened;
                    }
                }
                if ($word->modifiedBy !== $pendingCurrAmend) {
                    $data = new GroupedParagraphData();
                    $data->amendment = $pendingCurrAmend;
                    $data->text = $pending;
                    $groupedParaData[] = $data;

                    $pending          = '';
                    $pendingCurrAmend = $word->modifiedBy;
                }
                $pending .= $word->modification;

                if ($word->appendCollisionGroups) {
                    foreach ($word->appendCollisionGroups as $appendCollisionGroup) {
                        $appendedDiff = '';
                        $amendmentId = null;
                        foreach ($appendCollisionGroup->tokens as $token) {
                            $appendedDiff .= $token->diff;
                            $amendmentId = $token->amendmentId;
                        }

                        $cid = $CHANGESET_COUNTER++;
                        $mid  = $cid . '-' . $amendmentId . '-COLLISION';
                        $appendedDiff = str_replace('###INS_START###', '###INS_START' . $mid . '###', $appendedDiff);
                        $appendedDiff = str_replace('###DEL_START###', '###DEL_START' . $mid . '###', $appendedDiff);

                        $pending .= $appendedDiff;
                    }
                }
            } else {
                if (0 !== $pendingCurrAmend) {
                    $data = new GroupedParagraphData();
                    $data->amendment = $pendingCurrAmend;
                    $data->text = $pending;
                    $groupedParaData[] = $data;

                    $pending          = '';
                    $pendingCurrAmend = 0;
                }
                $pending .= $word->orig;
            }
        }

        $data = new GroupedParagraphData();
        $data->amendment = $pendingCurrAmend;
        $data->text = $pending;
        $groupedParaData[] = $data;

        return $groupedParaData;
    }

    /**
     * @return GroupedParagraphData[]
     */
    public function getGroupedParagraphData(?int &$CHANGESET_COUNTER = null): array
    {
        $this->merge();

        $words = $this->paraData->words;

        return static::groupParagraphData($words, $CHANGESET_COUNTER);
    }

    /**
     * @param Amendment[] $amendmentsById
     */
    public function getFormattedDiffText(array $amendmentsById): string
    {
        $CHANGESET_COUNTER = 0;
        $changeset         = [];

        $groupedParaData = $this->getGroupedParagraphData($CHANGESET_COUNTER);

        $paragraphText   = '';
        foreach ($groupedParaData as $part) {
            $text = $part->text;

            if ($part->amendment > 0) {
                $amendmentId = $part->amendment;
                $cid         = $CHANGESET_COUNTER++;
                if (!isset($changeset[$amendmentId])) {
                    $changeset[$amendmentId] = [];
                }
                $changeset[$amendmentId][] = $cid;

                $mid  = $cid . '-' . $amendmentId;
                $text = str_replace('###INS_START###', '###INS_START' . $mid . '###', $text);
                $text = str_replace('###DEL_START###', '###DEL_START' . $mid . '###', $text);
            }

            $paragraphText .= $text;
        }

        return DiffRenderer::renderForInlineDiff($paragraphText, $amendmentsById);
    }


    /*
     * Somewhat special case: if two amendments are inserting a bullet point at the same place,
     * they are colliding. We cannot change this fact right now, so at least
     * let's try not to print the previous line that wasn't actually changed twice.
     */
    private static function stripUnchangedLiFromColliding(string $str): string
    {
        if (mb_substr($str, 0, 8) !== '<ul><li>' && mb_substr($str, 0, 8) !== '<ol><li>') {
            return $str;
        }
        if (mb_substr_count($str, '<li>') !== 1 || mb_substr_count($str, '</li>') !== 1) {
            return $str;
        }
        return preg_replace('/<li>.*<\/li>/siu', '', $str);
    }

    /**
     * @return CollidingParagraphDiff[]
     */
    public function getCollidingParagraphs(): array
    {
        $this->merge();
        return $this->paraData->collidingParagraphs;
    }

    /**
     * @return GroupedParagraphData[][]
     */
    public function getCollidingParagraphGroups(): array
    {
        $this->merge();

        $grouped = [];

        foreach ($this->paraData->collidingParagraphs as $changeSet) {
            /** @var ParagraphMergerWord[] $words */
            $words = [];
            foreach ($this->paraData->origTokenized as $token) {
                $mergerWord = new ParagraphMergerWord();
                $mergerWord->orig = $token;
                $words[] = $mergerWord;
            }

            foreach ($changeSet->diff as $i => $token) {
                if ($token->amendmentId !== null) {
                    $words[$i]->modification = $token->diff;
                    $words[$i]->modifiedBy   = $token->amendmentId;
                }
            }

            $data = static::groupParagraphData($words);
            foreach ($data as $i => $dat) {
                if ($dat->amendment == 0) {
                    $data[$i]->text = static::stripUnchangedLiFromColliding($dat->text);
                }
            }
            $grouped[$changeSet->amendment] = $data;
        }

        return $grouped;
    }

    /**
     * @param GroupedParagraphData[] $paraData
     * @param Amendment[] $amendmentsById
     */
    public static function getFormattedCollision(array $paraData, Amendment $amendment, array $amendmentsById): string
    {
        $amendmentUrl      = UrlHelper::createAmendmentUrl($amendment);
        $paragraphText     = '';
        $CHANGESET_COUNTER = 0;

        foreach ($paraData as $part) {
            $text = $part->text;

            if ($part->amendment > 0) {
                $amendment = $amendmentsById[$part->amendment];
                $cid       = $CHANGESET_COUNTER++;

                $mid  = $cid . '-' . $amendment->id;
                $text = str_replace('###INS_START###', '###INS_START' . $mid . '###', $text);
                $text = str_replace('###DEL_START###', '###DEL_START' . $mid . '###', $text);
            }

            $paragraphText .= $text;
        }

        $out = '<div class="collidingParagraph collidingParagraph' . $amendment->id . '"
                     data-link="' . Html::encode($amendmentUrl) . '"
                     data-amendment-id="' . $amendment->id . '"
                     data-username="' . Html::encode($amendment->getInitiatorsStr()) . '">
                     <button class="btn btn-link pull-right btn-xs hideCollision" type="button">' .
               \Yii::t('amend', 'merge_colliding_hide') . ' <span class="glyphicon glyphicon-minus-sign"></span>' .
               '</button>
                     <p class="collidingParagraphHead"><strong>' .
            \Yii::t('amend', 'merge_colliding') . ': ' .
            Html::a(Html::encode($amendment->titlePrefix), $amendmentUrl) .
            '</strong></p>';

        $out .= '<div class="alert alert-danger"><p>' . \Yii::t('amend', 'merge_colliding_hint') . '</p></div>';
        $out .= DiffRenderer::renderForInlineDiff($paragraphText, $amendmentsById);
        $out .= '</div>';

        return $out;
    }

    /**
     * @return int[]
     */
    public function getAffectingAmendmentIds(): array
    {
        return array_map(function (ParagraphDiff $diff) {
            return $diff->amendment;
        }, $this->diffs);
    }
}
