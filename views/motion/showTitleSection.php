<?php
/**
 * @var \app\models\db\MotionSection $section
 * @var int[] $openedComments
 * @var null|CommentForm $commentForm
 */

use app\components\UrlHelper;
use app\models\db\ConsultationSettingsMotionSection;
use app\models\db\MotionComment;
use app\models\db\User;
use app\models\forms\CommentForm;
use app\views\motion\LayoutHelper;
use yii\helpers\Html;

$motion         = $section->getMotion();
$hasLineNumbers = $section->getSettings()->lineNumbers; // @TODO
$classes        = ['paragraph'];
if ($hasLineNumbers) {
    $classes[] = 'lineNumbers';
    $lineNo    = $section->getFirstLineNumber();
}

$id = 'section_' . $section->sectionId . '_0';
echo '<div class="' . implode(' ', $classes) . '" id="' . $id . '">';

$amendingSections = $section->getAmendingSections(false, true);

echo '<ul class="bookmarks">';

foreach ($amendingSections as $amendmentSection) {
    $amendment = \app\models\db\Consultation::getCurrent()->getAmendment($amendmentSection->amendmentId);
    $amLink    = UrlHelper::createAmendmentUrl($amendment);
    echo '<li class="amendment amendment' . $amendment->id . '" data-first-line="' . $lineNo . '">';
    echo '<a data-id="' . $amendment->id . '" href="' . Html::encode($amLink) . '">';
    echo Html::encode($amendment->titlePrefix) . "</a></li>\n";
}

echo '</ul>';

echo '<div class="text textOrig fixedWidthFont">';
if ($hasLineNumbers) {
    /** @var int $lineNo */
    $lineNoStr = '<span class="lineNumber" data-line-number="' . $lineNo++ . '"></span>';
}
echo Html::encode($section->data);
echo '</div>';

foreach ($amendingSections as $amendmentSection) {
    $amendment = \app\models\db\Consultation::getCurrent()->getAmendment($amendmentSection->amendmentId);
    echo '<div class="text textAmendment hidden fixedWidthFont amendment' . $amendment->id . '">';
    echo '<div class="preamble"><div>';
    echo '<h3>' . \Yii::t('amend', 'amendment') . ' ' . Html::encode($amendment->titlePrefix) . '</h3>';
    echo ', ' . \Yii::t('amend', 'initiated_by') . ': ' . Html::encode($amendment->getInitiatorsStr());
    $amParas = $amendment->getChangedParagraphs($motion->getActiveSections(), true);
    if (count($amParas) > 1) {
        echo '<div class="moreAffected">';
        echo str_replace('%num%', count($amParas), \Yii::t('amend', 'affects_x_paragraphs'));
        echo '</div>';
    }
    echo '</div></div>';
    echo str_replace('###LINENUMBER###', '', $amendmentSection->data);
    echo '</div>';
}

echo '</div>';
