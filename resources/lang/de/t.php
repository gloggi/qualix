<?php
return array(
    'footer' => array(
        'slogan' => 'Qualix. was gaffsch?',
    ),
    'global' => array(
        'add' => 'Hinzufügen',
        'save' => 'Speichern',
    ),
    'header' => array(
        'course_admin' => 'Kursadmin',
        'language_switch' => 'Sprache wechseln. Übersetzungen via Phrase.com',
        'qualix' => 'Qualix',
        'welcome' => 'Willkommen, :user',
    ),
    'models' => array(
        'block' => array(
            'block_date' => 'Datum',
            'full_block_number' => 'Blocknummer',
            'name' => 'Blockname',
            'num_observations' => 'Anzahl Beobachtungen',
            'requirements' => 'Mindestanforderungen',
        ),
        'category' => array(
            'name' => 'Titel',
            'num_observations' => 'Anzahl Beobachtungen',
        ),
        'participant' => array(
            'scout_name' => 'Pfadiname',
            'group' => 'Abteilung',
            'image' => 'Bild',
        ),
    ),
    'views' => array(
        'admin' => array(
            'blocks' => array(
                'are_blocks_required' => array(
                    'question' => 'Muss ich Blöcke für meinen Kurs erfassen?',
                    'answer' => 'Ja, jede Beobachtung gehört zu genau einem Block. Daher kannst du Qualix nur verwenden, wenn du Blöcke im Kurs erfasst hast. Falls du Beobachtungen ausserhalb der Blöcke machen willst, empfehlen wir, einen oder mehrere "Sonstiges"-Blöcke zu erfassen.',
                ),
                'edit' => 'Block bearbeiten',
                'existing' => 'Blöcke :courseName',
                'new' => 'Neuer Block',
                'no_blocks' => 'Bisher sind keine Blöcke erfasst.',
                'observations_on_block' => '{0}Es ist keine Beobachtung dazu erfasst.|{1}Es ist eine Beobachtung dazu erfasst.|[2,*] Es sind :count Beobachtungen dazu erfasst.',
                'really_delete' => 'Willst du den Block ":name" wirklich löschen?',
                'title' => 'Blöcke',
                'what_are_blocks' => array(
                    'question' => 'Was sind Blöcke?',
                    'answer' => 'Blöcke sind zeitliche Abschnitte im Grobprogramm. Man könnte sie auch Lektionen oder Programmeinheiten nennen. Du kannst zudem erfassen, welche Mindestanforderungen in einem Block wohl am ehesten beobachtet werden können (z.B. eine Mindestanforderung zu Sicherheitsüberlegungen in einem Block über Sicherheitskonzepte). Beim Erfassen von Beobachtungen kann das aber immer noch übersteuert werden.',
                )
            ),
            'categories' => array(
                'are_categories_required' => array(
                    'question' => 'Muss ich Kategorien für meinen Kurs erfassen?',
                    'answer' => 'Nein, Kategorien sind komplett optional, falls ihr in eurem Kursteam keine Verwendung dafür habt.',
                ),
                'edit' => 'Kategorie bearbeiten',
                'existing' => 'Kategorien :courseName',
                'new' => 'Neue Kategorie',
                'no_categories' => 'Bisher sind keine Kategorien erfasst.',
                'observations_on_category' => '{0}Es ist keine Beobachtung dazu erfasst.|{1}Es ist eine Beobachtung dazu erfasst.|[2,*] Es sind :count Beobachtungen dazu erfasst.',
                'really_delete' => 'Willst du die Kategorie ":name" wirklich löschen?',
                'title' => 'Kategorien',
                'what_are_categories' => array(
                    'question' => 'Was sind Kategorien?',
                    'answer' => 'Kategorien können auf verschiedene Art eingesetzt werden. Jeder Beobachtung kann eine, mehrere oder keine Kategorie zugewiesen werden. Das kann man zum Beispiel zur Einordnung in verschiedene Abschnitte eines Quali-Formulars verwenden (wenn die Abschnitte nicht sowieso den Mindestanforderungen entsprechen). Oder um zu markieren, ob eine Beobachtung schon im Zwischenquali angesprochen wurde. Oder noch ganz andere Anwendungen, die dir einfallen. Danach kannst du die Beobachtungs-Liste eines Teilnehmenden nach Kategorien filtern.',
                ),
            ),
            'course_settings' => array(
                'archive_vs_delete' => array(
                    'question' => 'Was ist der Unterschied?',
                    'answer' => 'Wenn du einen Kurs archivierst, werden alle personenbezogenen Daten (TN, Bilder, Beobachtungen) dauerhaft gelöscht. So kannst du Datenschutz-Problemen entgegenwirken, aber für spätere Kurse trotzdem noch deine alten Mindestanforderungen und Blöcke einsehen. Wenn du den Kurs hingegen ganz löschst wird alles was damit zu tun hat unwiderruflich entfernt.',
                ),
                'title' => 'Kurseinstellungen',
            ),
            'equipe' => array(
                'title' => 'Equipe',
            ),
            'new_course' => array(
                'title' => 'Neuen Kurs erstellen',
            ),
            'participants' => array(
                'edit' => 'TN ändern',
                'existing' => 'Teilnehmende :courseName',
                'new' => 'Neue Teilnehmende',
                'no_participants' => 'Bisher sind keine Teilnehmende erfasst.',
                'observations_on_participant' => '{0}Es ist keine Beobachtung zugeordnet.|{1} Es ist eine Beobachtung zugeordnet.|[2,*]Es sind :count Beobachtungen zugeordnet.',
                'really_remove' => 'Willst du :name wirklich aus deinem Kurs entfernen?',
                'title' => 'TN',
            ),
            'requirements' => array(
                'are_requirements_required' => array(
                    'question' => 'Muss ich Mindestanforderungen für meinen Kurs erfassen?',
                    'answer' => 'Es ist sehr wichtig, vor dem Kurs im Kursteam Mindestanforderungen festzulegen, damit alle Teilnehmenden nach dem gleichen Schema qualifiziert werden und damit Entscheide im Kurs einfacher gefällt werden können. Aber wenn du diese nicht in Qualix führen willst, kannst du Beobachtungen auch ohne Mindestanforderungen erfassen.',
                ),
                'title' => 'Mindestanforderungen',
                'what_are_requirements' => array(
                    'question' => 'Was sind Mindestanforderungen?',
                    'answer' => 'Mindestanforderungen sind klare Voraussetzungen und Kriterien, die alle Teilnehmenden während dem Kurs erfüllen sollen. Anhand der Mindestanforderungen wird beurteilt, wer den Kurs besteht und wer nicht. Du kannst Mindestanforderungen als Killer-Kriterien markieren wenn du willst, aber es hat momentan keine Auswirkungen in Qualix selber (bis auf eine etwas andere Farbgebung).',
                ),
            ),
        ),
        'blocks' => array(
            'title' => 'Blöcke',
        ),
        'crib' => array(
            'here' => 'hier',
            'title' => 'Spick',
            'see_only_empty_blocks' => array(
                'question' => 'Siehst du nur leere Blöcke ohne Mindestanforderungen?',
                'answer' => 'Dann sind bisher keine Blöcke mit Mindesanforderungen verbunden. Bitte verbinde die Blöcke :here mit Mindestanforderungen.',
            ),
        ),
        'overview' => array(
            'title' => 'Überblick',
        ),
        'participants' => array(
            'title' => 'TN',
        ),
    ),
);
