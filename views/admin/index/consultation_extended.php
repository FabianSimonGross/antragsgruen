<?php

/**
 * @var $this yii\web\View
 * @var Consultation $consultation
 */
use app\components\AntiXSS;
use app\components\UrlHelper;
use app\models\db\Consultation;
use yii\helpers\Html;

/** @var \app\controllers\admin\IndexController $controller */
$controller = $this->context;
$layout     = $controller->layoutParams;

$layout->addCSS('/css/backend.css');

$this->title = 'Einstellungen';
$layout->addBreadcrumb('Administration', UrlHelper::createUrl('admin/index'));
$layout->addBreadcrumb('Erweitert');

echo '<h1>Erweiterte Einstellungen</h1>';

echo Html::beginForm('', 'post', ['id' => 'consultationSettingsForm', 'class' => 'adminForm form-horizontal']);

echo $controller->showErrors();

$settings        = $consultation->getSettings();
$handledSettings = [];

/**
 * @param \app\models\settings\Consultation $settings
 * @param string $field
 * @param array $handledSettings
 * @param string $description
 */
$booleanSettingRow = function ($settings, $field, &$handledSettings, $description) {
    $handledSettings[] = $field;
    echo '<fieldset><label>';
    echo Html::checkbox('settings[' . $field . ']', $settings->$field, ['id' => $field]);
    echo $description;
    echo '</label></fieldset>';
};


echo '<h3 class="green">Allgemeine Einstellungen zur Veranstaltung</h3>';
echo '<div class="content">';


$layout = $consultation->site->getSettings()->siteLayout;
echo '<fieldset class="form-group">
    <label class="col-sm-3 control-label" for="consultationPath">Layout:</label>
    <div class="col-sm-9">';
echo Html::dropDownList(
    'siteSettings[siteLayout]',
    $layout,
    \app\models\settings\Layout::getCssLayouts(),
    ['id' => 'siteLayout', 'class' => 'form-control']
);
echo '</div></fieldset>';


$handledSettings[] = 'logoUrl';
echo '<fieldset class="form-group">
    <label class="col-sm-3 control-label" for="consultationPath">Logo-URL:</label>
    <div class="col-sm-9">
        <input type="text" name="settings[logoUrl]"
        value="' . Html::encode($settings->logoUrl) . '" class="form-control" id="logoUrl">
    </div>
</fieldset>';
// <small>Im Regelfall einfach leer lassen. Falls eine URL angegeben wird, wird das angegebene Bild statt dem
// großen "Antragsgrün"-Logo angezeigt.

$handledSettings[] = 'logoUrlFB';
echo '<fieldset class="form-group">
    <label class="col-sm-3 control-label" for="consultationPath">Facebook-Bild:</label>
    <div class="col-sm-9">
        <input type="text" name="settings[logoUrlFB]"
        value="' . Html::encode($settings->logoUrlFB) . '" class="form-control" id="logoUrlFB">
    </div>
</fieldset>';
// <small>Dieses Bild erscheint, wenn etwas auf dieser Seite bei Facebook geteilt wird. Vorsicht: nachträglich
// ändern ist oft heikel, da FB viel zwischenspeichert.


$handledSettings[] = 'pdfIntroduction';
$placeholder = '26. Ordentliche Bundesdelegiertenkonferenz von BÜNDNIS 90/DIE GRÜNEN,' . "\n" .
    '01.-03. Dezember 2006. Kölnmesse, Köln-Deutz';
echo '<fieldset class="form-group">
    <label class="col-sm-3 control-label" for="pdfIntroduction">PDF-Einleitung:</label>
    <div class="col-sm-9">
        <textarea name="settings[pdfIntroduction]" class="form-control" id="pdfIntroduction"
        placeholder="' . Html::encode($placeholder) . '">' . $settings->pdfIntroduction . '</textarea>
    </div>
</fieldset>';
// <small>Im Regelfall einfach leer lassen. Falls eine URL angegeben wird, wird das angegebene Bild statt dem
// großen "Antragsgrün"-Logo angezeigt.


$handledSettings[] = 'lineLength';
echo '<fieldset class="form-group">
    <label class="col-sm-3 control-label" for="consultationPath">Maximale Zeilenlänge:</label>
    <div class="col-sm-3">
        <input type="text" required name="settings[lineLength]"
        value="' . Html::encode($settings->lineLength) . '" class="form-control" id="lineLength">
    </div>
</fieldset>';
// NICHT ändern, nachdem Anträge eingereicht wurden, weil sich dann die Zeilennummern ändern!

$description = '<strong>Antragskürzel verstecken</strong><br>
<small style="margin-left: 20px; display: block;">(Antragskürzel wie z.B. "A1", "A2", "Ä1neu" etc.)
müssen zwar weiterhin angegeben werden, damit danach sortiert werden kann. Es wird aber nicht mehr angezeigt.
Das ist dann praktisch, wenn man eine eigene Nummerierung im Titel der Anträge vornimmt.</small>';

$booleanSettingRow($settings, 'hideRevision', $handledSettings, $description);


$booleanSettingRow($settings, 'showFeeds', $handledSettings, 'Feeds in der Sidebar anzeigen');

$description = '<strong>Minimalistische Ansicht</strong><br>
<small style="margin-left: 20px;">Der Login-Button und der Info-Header über den Anträgen werden versteckt.</small>';
$booleanSettingRow($settings, 'minimalisticUI', $handledSettings, $description);


echo '</div>
<h2 class="green">Anträge</h2>
<div class="content">';

$tags = $consultation->getSortedTags();
echo '<fieldset class="form-group">
<div class="col-sm-4 control-label">Schlagworte:</div>
<div class="col-sm-8">';
if (count($tags) > 0) {
    echo '<ul class="taglist">';
    $delUrl = UrlHelper::createUrl(['admin/index/consultationextended', AntiXSS::createToken('delTag') => '#ID#']);
    foreach ($tags as $tag) {
        echo '<li><input type="hidden" name="tagSort[]" value="' . $tag->id . '">';
        echo '<span class="sortable" style="cursor: move;">' . Html::encode($tag->title) . '</span>';
        echo '(' . count($tag->motions) . ')';
        if (count($tag->motions) == 0) {
            echo ' <a href="' . Html::encode(str_replace(urlencode('#ID#'), $tag->id, $delUrl)) .
                '" onClick="return confirm(\'Wirklich löschen?\');" style="color: red; font-size: 0.8em;">löschen</a>';
        }
        echo '</li>';
    }
    echo '</ul>';
} else {
    echo '<em>Keine</em> &nbsp; &nbsp;';
}

$handledSettings[] = 'allowMultipleTags';
echo '<a href="#" class="tagCreateOpener">+ Neues hinzufügen</a>
    <input class="tagCreateInput" name="tagCreate" placeholder="Neues Schlagwort" value="" style="display: none;">
    <br><br>
    <label style="width: auto;">';

echo Html::checkbox('settings[allowMultipleTags]', $settings->allowMultipleTags, ['id' => 'allowMultipleTags']);
echo 'Mehrere Schlagworte pro Antrag möglich</label>

</div>
</fieldset>';


echo '<script>
            $(".tagCreateOpener").click(function (ev) {
                ev.preventDefault();
                $(".tagCreateInput").show().focus();
                $(this).hide();
            });
            $(".taglist").sortable({
                containment: "parent",
                axis: "y"
            });
        </script>';


$description = 'Admins dürfen Antrags-Texte <strong>nachträglich ändern</strong>.';
$booleanSettingRow($settings, 'adminsMayEdit', $handledSettings, $description);

$description = 'AntragstellerInnen dürfen Anträge <strong>nachträglich ändern</strong>.';
$booleanSettingRow($settings, 'iniatorsMayEdit', $handledSettings, $description);


// @TODO
echo '
    <script>
        $(function () {
            var $admins_duerfen = $("#admins_duerfen_aendern").find("input"),
                $initiatorInnen = $("#initiatorInnen_duerfen_aendern");
            $admins_duerfen.change(function () {
                if ($(this).prop("checked")) {
                    $initiatorInnen.show();
                } else {
                    if (!confirm("Wenn dies deaktiviert wird, wirkt sich das auch auf alle bisherigen Anträge aus " +
                     "und kann für bisherige Anträge nicht rückgängig gemacht werden. Wirklich setzen?")) {
                        $(this).prop("checked", true);
                    } else {
                        $initiatorInnen.hide();
                        $initiatorInnen.find("input").prop("checked", false);
                    }
                }
            });
            if (!$admins_duerfen.prop("checked")) $initiatorInnen.hide();
        })
    </script>
';

$description = 'Kommentare zum Antrag allgemein zulassen<br>
<small style="margin-left: 20px;">(Anträge ohne Absatzbezug, erscheinen unterhalb des Antrags)</small>';
$booleanSettingRow($settings, 'commentWholeMotions', $handledSettings, $description);


$description = 'Anträge (ausgegraut) anzeigen, auch wenn sie noch nicht freigeschaltet sind';
$booleanSettingRow($settings, 'screeningMotionsShown', $handledSettings, $description);


$description = 'Durchgestrichen als Formatierungsmöglichkeit in Anträgen zulassen';
$booleanSettingRow($settings, 'allowStrikeFormat', $handledSettings, $description);


echo '</div>';

/*
echo '<h2 class="green">Kommentare</h2>
<div class="content">';


$description = 'Besucher können Kommentare <strong>bewerten</strong>';
$booleanSettingRow($settings, 'commentsSupportable', $handledSettings, $description);

echo '</div>';
*/

echo '<div class="saveholder">
<button type="submit" name="save" class="btn btn-primary">Speichern</button>
</div>


</div>';


foreach ($handledSettings as $setting) {
    echo '<input type="hidden" name=settingsFields[]" value="' . Html::encode($setting) . '">';
}

echo Html::endForm();
