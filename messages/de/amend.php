<?php

return [
    'amendment'                         => 'Änderungsantrag',
    'amendments'                        => 'Änderungsanträge',
    'motion'                            => 'Antrag',
    'initiator'                         => 'Antragsteller*in',
    'initiators_title'                  => 'Antragsteller*innen',
    'supporter'                         => 'Unterstützer*in',
    'supporters'                        => 'Unterstützer*innen',
    'supporters_title'                  => 'Unterstützer*innen',
    'supporter_you'                     => 'Du!',
    'supporter_none'                    => 'keine',
    'status'                            => 'Status',
    'resoluted_on'                      => 'Entschieden am',
    'submitted_on'                      => 'Eingereicht',
    'comments_title'                    => 'Kommentare',
    'comments_screening_queue_1'        => '1 Kommentar wartet auf Freischaltung',
    'comments_screening_queue_x'        => '%NUM% Kommentare warten auf Freischaltung',
    'comments_please_log_in'            => 'Logge dich ein, um kommentieren zu können.',
    'prefix'                            => 'Antragsnummer',
    'none_yet'                          => 'Es gibt noch keine Änderungsanträge',
    'amendment_for'                     => 'Änderungsantrag zu',
    'amendment_for_prefix'              => 'Änderungsantrag zu %PREFIX%',
    'confirmed_visible'                 => 'Du hast den Änderungsantrag eingereicht. Er ist jetzt sofort sichtbar.',
    'confirmed_screening'               => 'Du hast den Änderungsantrag eingereicht. ' .
        'Er wird nun auf formale Richtigkeit geprüft und dann freigeschaltet.',
    'submitted_adminnoti_title'         => 'Neuer Änderungsantrag',
    'submitted_adminnoti_body'          => "Es wurde ein neuer Änderungsantrag eingereicht.\nAntrag: %TITLE%\nAntragsteller*in: %INITIATOR%\nLink: %LINK%",
    'submitted_screening_email'         => "Hallo,\n\ndu hast soeben einen Änderungsantrag eingereicht.\n" .
        "Der Antrag wird nun auf formale Richtigkeit geprüft und dann freigeschaltet. " .
        "Du wirst dann gesondert darüber benachrichtigt.\n\n" .
        "Du kannst ihn hier einsehen: %LINK%\n\n" .
        "Mit freundlichen Grüßen,\n" .
        "  Das Antragsgrün-Team",
    'submitted_screening_email_subject' => 'Änderungsantrag eingereicht',
    'screened_hint'                     => 'Geprüft',
    'amend_for'                         => ' zu ',
    'create_explanation'                => 'Ändere hier den Antrag so ab, wie du ihn gern sehen würdest.<br>' . "\n" .
        'Unter &quot;<strong>Begründung</strong>&quot; kannst du die Änderung begründen.<br>' . "\n" .
        'Falls dein Änderungsantrag Hinweise an die Programmkommission enthält, kannst du diese als ' . "\n" .
        '&quot;<strong>Redaktionelle Änderung</strong>&quot; beifügen.',
    'editorial_hint'                    => 'Redaktionelle Änderung',
    'merge_amend_stati'                 => 'Status der Änderungsanträge',
    'merge_bread'                       => 'Überarbeiten',
    'merge_title'                       => '%TITLE% überarbeiten',
    'merge_new_text'                    => 'Neuer Antragstext',
    'merge_confirm_title'               => 'Überarbeitung kontrollieren',
    'merge_submitted'                   => 'Überarbeitung eingereicht',
    'merge_submitted_title'             => '%TITLE% überarbeitet',
    'merge_submitted_str'               => 'Der Antrag wurde überarbeitet',
    'merge_submitted_to_motion'         => 'Zum neuen Antrag',
    'merge_colliding'                   => 'Kollidierender Änderungsantrag',
    'merge_accept_all'                  => 'Alle Änderungen übernehmen',
    'merge_reject_all'                  => 'Alle Änderungen ablehnen',
    'merge_explanation'                 => 'Hier wird der Text mitsamt allen Änderungsanträgen im Text angezeigt. ' .
        'Du kannst bei jeder Änderung angeben, ob du sie <strong>annehmen oder ablehnen</strong> willst - klicke dazu einfach mit der rechten Maustaste auf die Änderung und wähle "Annehmen" oder "Ablehnen" aus.<br><br>' .
        'Über das Annehmen und Ablehnen von Änderungsanträgen hinaus kannst du den Text auch <strong>frei bearbeiten</strong>, um dadurch redaktionelle Änderungen durchzuführen.<br>###COLLIDINGHINT###<br><br>' .
        'Anschließend kannst du den neuen Status der Änderungsanträge auswählen und dann auf "Weiter" klicken. Dadurch wird ein <strong>neuer Antrag "###NEWPREFIX###"</strong> erzeugt. Der ursprüngliche Antrag sowie die Änderungsanträge bleiben zur Referenz erhalten, werden aber als "veraltet" markiert.',
    'merge_explanation_colliding'       => '<br><span class="glyphicon glyphicon-warning-sign" style="float: left; font-size: 2em; margin: 10px;"></span> Da es zu diesem Antrag mehrere Änderungsanträge gibt, die sich auf die selbe Textstelle beziehen - <strong>kollidierende Änderungsanträge</strong> - ist es notwendig, diese Änderungsanträge händisch einzupflegen. Lösche bitte anschließend den kollidierenden Änderungsantrag, indem du ihn zunächst mit der Entfernen/Del-Taste löschst, und diese Änderung dann mit der rechten Maustaste annimmst.',
    'unsaved_drafts'                    => 'Es gibt noch ungespeicherte Entwürfe, die wiederhergestellt werden können:',
    'confirm_amendment'                 => 'Änderungsantrag bestätigen',
    'amendment_submitted'               => 'Änderungsantrag eingereicht',
    'amendment_create'                  => 'Änderungsantrag stellen',
    'amendment_edit'                    => 'Änderungsantrag bearbeiten',
    'amendment_create_x'                => 'Änderungsantrag zu %prefix% stellen',
    'amendment_edit_x'                  => 'Änderungsantrag zu %prefix% bearbeiten',
    'amendment_withdraw'                => 'Änderungsantrag zurückziehen',
    'edit_done'                         => 'Änderungsantrag bearbeitet',
    'edit_done_msg'                     => 'Die Änderungen wurden übernommen.',
    'edit_bread'                        => 'Bearbeiten',
    'reason'                            => 'Begründung',
    'amendment_requirement'             => 'Voraussetzungen für einen Antrag',
    'button_submit'                     => 'Einreichen',
    'button_correct'                    => 'Korrigieren',
    'confirm'                           => 'Bestätigen',
    'go_on'                             => 'Weiter',
    'published_email_body'              => "Hallo,\n\ndein Änderungsantrag wurde soeben auf Antragsgrün veröffentlicht. " .
        "Du kannst ihn hier einsehen: %LINK%\n\n" .
        "Mit freundlichen Grüßen,\n" .
        "  Das Antragsgrün-Team",
    'published_email_title'             => 'Änderungsantrag veröffentlicht',
    'sidebar_adminedit'                 => 'Admin: bearbeiten',
    'sidebar_back'                      => 'Zurück zum Antrag',
    'back_to_amend'                     => 'Zurück zum Änderungsantrag',
    'initiated_by'                      => 'gestellt von',
    'confirm_bread'                     => 'Bestätigen',
    'affects_x_paragraphs'              => 'Bezieht sich auf insgesamt %num% Absätze',
    'singlepara_revert'                 => 'Änderungen rückgängig machen',
    'err_create_permission'             => 'Keine Berechtigung zum Anlegen von Änderungsanträgen.',
    'err_create'                        => 'Ein Fehler beim Anlegen ist aufgetreten',
    'err_save'                          => 'Ein Fehler beim Speichern ist aufgetreten',
    'err_type_missing'                  => 'Du musst einen Typ angeben.',
    'err_not_found'                     => 'Der Änderungsantrag wurde nicht gefunden',
    'err_withdraw_forbidden'            => 'Not allowed to withdraw this motion.',
    'err_edit_forbidden'                => 'Not allowed to edit this motion.',
    'withdraw_done'                     => 'Der Änderungsantrag wurde zurückgezogen.',
    'withdraw_bread'                    => 'Zurückziehen',
    'withdraw'                          => 'Zurückziehen',
    'withdraw_confirm'                  => 'Willst du diesen Änderungsantrag wirklich zurückziehen?',
    'withdraw_no'                       => 'Doch nicht',
    'withdraw_yes'                      => 'Zurückziehen',
    'widthdraw_done'                    => 'Der Änderungsantrag wurde zurückgezogen.',
    'title_amend_to'                    => 'Ändern in',
    'title_new'                         => 'Neuer Titel',
    'amend_like_done'                   => 'Du stimmst diesem Änderungsantrag nun zu.',
    'amend_dislike_done'                => 'Du lehnst diesen Änderungsantrag nun ab.',
    'amend_neutral_done'                => 'Du stehst diesem Änderungsantrag wieder neutral gegenüber.',
];
