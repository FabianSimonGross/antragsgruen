<?php

namespace app\models\db;

use app\components\UrlHelper;
use app\models\settings\AntragsgruenApp;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int|null $id
 * @property int|null $motionTypeId
 * @property int|null $consultationId
 * @property int $siteId
 * @property string $category
 * @property string $textId
 * @property int $menuPosition
 * @property string|null $title
 * @property string|null $breadcrumb
 * @property string|null $text
 * @property string $editDate
 *
 * @property ConsultationMotionType|null $motionType
 * @property Consultation|null $consultation
 * @property Site|null $site
 */
class ConsultationText extends ActiveRecord
{
    public const DEFAULT_CATEGORY = 'pagedata';

    public const DEFAULT_PAGE_WELCOME = 'welcome';
    public const DEFAULT_PAGE_DOCUMENTS = 'documents';
    public const DEFAULT_PAGE_MAINTENANCE = 'maintenance';
    public const DEFAULT_PAGE_HELP = 'help';
    public const DEFAULT_PAGE_PRIVACY = 'privacy';
    public const DEFAULT_PAGE_LEGAL = 'legal';
    public const DEFAULT_PAGE_FEEDS = 'feeds';
    public const DEFAULT_PAGE_LOGIN_PRE = 'login_pre';
    public const DEFAULT_PAGE_LOGIN_POST = 'login_post';

    public static function tableName(): string
    {
        return AntragsgruenApp::getInstance()->tablePrefix . 'consultationText';
    }

    public function getMotionType(): ActiveQuery
    {
        return $this->hasOne(ConsultationMotionType::class, ['id' => 'motionTypeId']);
    }

    public function getConsultation(): ActiveQuery
    {
        return $this->hasOne(Consultation::class, ['id' => 'consultationId']);
    }

    public function getSite(): ActiveQuery
    {
        return $this->hasOne(Site::class, ['id' => 'siteId']);
    }

    public function rules(): array
    {
        return [
            [['category', 'textId'], 'required'],
            [['category', 'textId', 'text', 'breadcrumb', 'title'], 'safe'],
            [['menuPosition'], 'number'],
        ];
    }

    public function getUrl(): string
    {
        $params = ['/pages/show-page', 'pageSlug' => $this->textId];
        if ($this->consultationId) {
            $params['consultationPath'] = $this->consultation->urlPath;
        }

        if ($this->textId === 'feeds') {
            return UrlHelper::createUrl(['consultation/feeds']);
        } else {
            return UrlHelper::createUrl($params);
        }
    }

    public function getSaveUrl(): string
    {
        $saveParams = ['/pages/save-page', 'pageSlug' => $this->textId];
        if ($this->consultation) {
            $saveParams['consultationPath'] = $this->consultation->urlPath;
        }
        if ($this->id) {
            $saveParams['pageId'] = $this->id;
        }

        return UrlHelper::createUrl($saveParams);
    }

    public function getUploadUrl(): ?string
    {
        if ($this->consultation) {
            $saveParams = ['/pages/upload', 'consultationPath' => $this->consultation->urlPath];
        } elseif ($this->site) {
            $saveParams = ['/pages/upload', 'consultationPath' => $this->site->currentConsultation->urlPath];
        } else {
            return null;
        }

        return UrlHelper::createUrl($saveParams);
    }

    public function getFileDeleteUrl(): ?string
    {
        if ($this->consultation) {
            $saveParams = ['/pages/delete-file', 'consultationPath' => $this->consultation->urlPath];
        } elseif ($this->site) {
            $saveParams = ['/pages/delete-file', 'consultationPath' => $this->site->currentConsultation->urlPath];
        } else {
            return null;
        }

        return UrlHelper::createUrl($saveParams);
    }

    public function getImageBrowseUrl(): string
    {
        return UrlHelper::createUrl(['/pages/browse-images']);
    }

    /**
     * @return string[]
     */
    public static function getDefaultPages(): array
    {
        return [
            self::DEFAULT_PAGE_MAINTENANCE => \Yii::t('pages', 'content_maint_title'),
            self::DEFAULT_PAGE_HELP => \Yii::t('pages', 'content_help_title'),
            self::DEFAULT_PAGE_LEGAL => \Yii::t('pages', 'content_imprint_title'),
            self::DEFAULT_PAGE_PRIVACY => \Yii::t('pages', 'content_privacy_title'),
            self::DEFAULT_PAGE_WELCOME => \Yii::t('pages', 'content_welcome'),
            self::DEFAULT_PAGE_LOGIN_PRE => \Yii::t('pages', 'content_login_pre'),
            self::DEFAULT_PAGE_LOGIN_POST => \Yii::t('pages', 'content_login_post'),
            self::DEFAULT_PAGE_FEEDS => \Yii::t('pages', 'content_feeds_title'),
        ];
    }

    /**
     * @return string[]
     */
    public static function getSitewidePages(): array
    {
        return [self::DEFAULT_PAGE_LEGAL, self::DEFAULT_PAGE_PRIVACY, self::DEFAULT_PAGE_LOGIN_PRE, self::DEFAULT_PAGE_LOGIN_POST];
    }

    /**
     * Pages that have a fallback for the whole system. Only relevant in multi-site-setups.
     *
     * @return string[]
     */
    public static function getSystemwidePages(): array
    {
        return [self::DEFAULT_PAGE_LEGAL, self::DEFAULT_PAGE_PRIVACY];
    }

    public static function getDefaultPage(string $pageKey): ConsultationText
    {
        $data           = new ConsultationText();
        $data->textId   = $pageKey;
        $data->category = self::DEFAULT_CATEGORY;
        switch ($pageKey) {
            case self::DEFAULT_PAGE_MAINTENANCE:
                $data->title      = \Yii::t('pages', 'content_maint_title');
                $data->breadcrumb = \Yii::t('pages', 'content_maint_bread');
                $data->text       = \Yii::t('pages', 'content_maint_text');
                break;
            case self::DEFAULT_PAGE_HELP:
                $data->title      = \Yii::t('pages', 'content_help_title');
                $data->breadcrumb = \Yii::t('pages', 'content_help_bread');
                $data->text       = \Yii::t('pages', 'content_help_place');
                break;
            case self::DEFAULT_PAGE_LEGAL:
                $data->title      = \Yii::t('pages', 'content_imprint_title');
                $data->breadcrumb = \Yii::t('pages', 'content_imprint_bread');
                $data->text       = '<p>' . \Yii::t('pages', 'content_imprint_title') . '</p>';
                break;
            case self::DEFAULT_PAGE_PRIVACY:
                $data->title      = \Yii::t('pages', 'content_privacy_title');
                $data->breadcrumb = \Yii::t('pages', 'content_privacy_bread');
                $data->text       = '';
                break;
            case self::DEFAULT_PAGE_WELCOME:
                $data->title      = \Yii::t('pages', 'content_welcome');
                $data->breadcrumb = \Yii::t('pages', 'content_welcome');
                $data->text       = \Yii::t('pages', 'content_welcome_text');
                break;
            case self::DEFAULT_PAGE_LOGIN_PRE:
                $data->title      = \Yii::t('pages', 'content_login_pre');
                $data->breadcrumb = \Yii::t('pages', 'content_login_pre');
                $data->text       = '';
                break;
            case self::DEFAULT_PAGE_LOGIN_POST:
                $data->title      = \Yii::t('pages', 'content_login_post');
                $data->breadcrumb = \Yii::t('pages', 'content_login_post');
                $data->text       = '';
                break;
            case self::DEFAULT_PAGE_FEEDS:
                $data->title      = \Yii::t('pages', 'content_feeds_title');
                $data->breadcrumb = \Yii::t('pages', 'content_feeds_title');
                $data->text       = \Yii::t('pages', 'content_feeds_text');
                break;
            case self::DEFAULT_PAGE_DOCUMENTS:
                $data->text = \Yii::t('pages', 'documents_intro_text');
                break;
        }

        return $data;
    }

    /**
     * @return ConsultationText[]
     */
    public static function getMenuEntries(?Site $site, ?Consultation $consultation): array
    {
        $pages = [];
        if ($site) {
            $pages = array_merge($pages, ConsultationText::findAll(['siteId' => $site->id, 'consultationId' => null]));
        }
        if ($consultation) {
            $pages = array_merge($pages, ConsultationText::findAll(['consultationId' => $consultation->id]));
        }
        $pages = array_filter($pages, function (ConsultationText $page) {
            if ($page->textId === 'help' && $page->text === \Yii::t('pages', 'content_help_place')) {
                return false;
            }

            return $page->menuPosition !== null;
        });
        usort($pages, function (ConsultationText $page1, ConsultationText $page2) {
            return $page1->menuPosition <=> $page2->menuPosition;
        });

        return $pages;
    }

    public static function getPageData(?Site $site, ?Consultation $consultation, string $pageKey): ConsultationText
    {
        $foundText = null;
        if (!in_array($pageKey, static::getSitewidePages())) {
            foreach ($consultation->texts as $text) {
                if ($text->category === self::DEFAULT_CATEGORY && mb_strtolower($text->textId) === mb_strtolower($pageKey)) {
                    $foundText = $text;
                }
            }
        }
        if (!$foundText) {
            $siteId    = ($site ? $site->id : null);
            $foundText = ConsultationText::findOne([
                'siteId'         => $siteId,
                'consultationId' => null,
                'category'       => self::DEFAULT_CATEGORY,
                'textId'         => $pageKey,
            ]);
        }
        if (!$foundText && in_array($pageKey, static::getSystemwidePages())) {
            $template = ConsultationText::findOne([
                'siteId' => null,
                'category' => self::DEFAULT_CATEGORY,
                'textId' => $pageKey,
            ]);
            if (!$template) {
                $template = static::getDefaultPage($pageKey);
            }
            $foundText = new ConsultationText();
            $foundText->category = self::DEFAULT_CATEGORY;
            $foundText->textId = $pageKey;
            $foundText->text = $template->text;
            $foundText->breadcrumb = $template->breadcrumb;
            $foundText->title = $template->title;
            if ($site) {
                $foundText->siteId = $site->id;
            }
        }
        $defaultPage = static::getDefaultPage($pageKey);
        if (!$foundText) {
            $foundText = $defaultPage;
            if (!in_array($pageKey, static::getSystemwidePages())) {
                $foundText->siteId = ($site ? $site->id : null);
            }
            if (!in_array($pageKey, static::getSitewidePages())) {
                $foundText->consultationId = ($consultation ? $consultation->id : null);
            }
        } else {
            if (!$foundText->breadcrumb) {
                $foundText->breadcrumb = $defaultPage->breadcrumb;
            }
            if (!$foundText->title) {
                $foundText->title = $defaultPage->title;
            }
        }

        return $foundText;
    }

    /**
     * @return ConsultationText[]
     */
    public static function getAllPages(Site $site, ?Consultation $consultation): array
    {
        $pages = ConsultationText::findAll(['siteId' => $site->id, 'consultationId' => null, 'category' => self::DEFAULT_CATEGORY]);
        if ($consultation) {
            $pages = array_merge(
                $pages,
                ConsultationText::findAll(['consultationId' => $consultation->id, 'category' => self::DEFAULT_CATEGORY])
            );
        }
        usort($pages, function ($page1, $page2) {
            return strnatcasecmp($page1->title ?? '', $page2->title ?? '');
        });

        return $pages;
    }
}
