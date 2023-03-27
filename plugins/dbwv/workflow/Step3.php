<?php

declare(strict_types=1);

namespace app\plugins\dbwv\workflow;

use app\components\RequestContext;
use app\models\db\Motion;
use app\models\exceptions\Access;
use app\models\settings\{PrivilegeQueryContext, Privileges};

class Step3
{

    public static function renderMotionAdministration(Motion $motion): string
    {
        $html = '';

        /*
        if (Step2::canSetRecommendation($motion)) {
            $html .= RequestContext::getController()->renderPartial(
                '@app/plugins/dbwv/views/admin_step_2_edit', ['motion' => $motion]
            );
        }
        */

        if (Workflow::canSetResolutionV3($motion)) {
            $html .= RequestContext::getController()->renderPartial(
                '@app/plugins/dbwv/views/admin_step_3_next', ['motion' => $motion]
            );
        }

        return $html;
    }

    public static function gotoNext(Motion $motion, array $postparams): void
    {
        if (!Workflow::canSetResolutionV3($motion)) {
            throw new Access('Not allowed to perform this action (generally)');
        }
        if ($motion->version !== Workflow::STEP_V3) {
            throw new Access('Not allowed to perform this action (in this state)');
        }

        $motion->version = Workflow::STEP_V4;
        $motion->save();
    }
}
