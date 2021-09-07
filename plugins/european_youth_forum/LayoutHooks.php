<?php

namespace app\plugins\european_youth_forum;

use app\models\db\Consultation;
use app\models\layoutHooks\Hooks;
use app\models\settings\VotingData;

class LayoutHooks extends Hooks
{
    public function getVotingAlternativeAdminResults(?string $before, Consultation $consultation): ?string
    {
        return file_get_contents(__DIR__ . '/views/voting-result-admin.vue.php');
    }

    public function getVotingAlternativeUserResults(?array $before, VotingData $votingData): ?array
    {
        return require(__DIR__ . '/views/voting-result-user.php');
    }
}