<?php

namespace app\plugins\neos;

use app\models\db\Consultation;
use app\models\db\Site;
use app\models\layoutHooks\Hooks;
use app\models\settings\Layout;
use app\models\siteSpecificBehavior\DefaultBehavior;
use app\plugins\ModuleBase;
use yii\web\View;

class Module extends ModuleBase
{
    /**
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @param View|null $view
     * @return array
     */
    public static function getProvidedLayouts($view = null)
    {
        if ($view) {
            $asset     = ThumbnailAssets::register($view);
            $thumbBase = $asset->baseUrl;
        } else {
            $thumbBase = '/';
        }

        return [
            'std' => [
                'title'   => 'NEOS',
                'preview' => $thumbBase . '/layout-preview-neos.png',
                'bundle'  => Assets::class,
            ]
        ];
    }

    /**
     * @return null|string
     */
    public static function overridesDefaultLayout()
    {
        return 'layout-plugin-neos-std';
    }

    /**
     * @param Consultation $consultation
     * @return string|ConsultationSettings
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function getConsultationSettingsClass($consultation)
    {
        return ConsultationSettings::class;
    }

    /**
     * @param Site $site
     * @return null|DefaultBehavior|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function getSiteSpecificBehavior($site)
    {
        return SiteSpecificBehavior::class;
    }

    /**
     * @param Layout $layoutSettings
     * @param Consultation $consultation
     * @return Hooks[]
     */
    public static function getForcedLayoutHooks($layoutSettings, $consultation)
    {
        return [
            new LayoutHooks($layoutSettings, $consultation)
        ];
    }

    /**
     * @return array
     */
    public static function getDefaultLogo()
    {
        return [
            'image/png',
            \Yii::$app->basePath . '/plugins/neos/assets/neos-antragsschmiede.png'
        ];
    }
}
