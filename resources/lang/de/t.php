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
        'participant' => array(
            'scout_name' => 'Pfadiname',
            'group' => 'Abteilung',
            'image' => 'Bild',
        ),
    ),
    'views' => array(
        'admin' => array(
            'course_settings' => array(
                'title' => 'Kurseinstellungen',
            ),
            'equipe' => array(
                'title' => 'Equipe',
            ),
            'requirements' => array(
                'title' => 'Mindestanforderungen',
            ),
            'categories' => array(
                'title' => 'Kategorien',
            ),
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
            'participants' => array(
                'edit' => 'TN ändern',
                'existing' => 'Teilnehmende :courseName',
                'new' => 'Neue Teilnehmende',
                'no_participants' => 'Bisher sind keine Teilnehmende erfasst.',
                'observations_on_participant' => '{0}Es ist keine Beobachtung zugeordnet.|{1} Es ist eine Beobachtung zugeordnet.|[2,*]Es sind :count Beobachtungen zugeordnet.',
                'really_remove' => 'Willst du :name wirklich aus deinem Kurs entfernen?',
                'title' => 'TN',
            ),
            'new_course' => array(
                'title' => 'Neuen Kurs erstellen',
            ),
        ),
        'blocks' => array(
            'title' => 'Blöcke',
        ),
        'participants' => array(
            'title' => 'TN',
        ),
        'overview' => array(
            'title' => 'Überblick',
        ),
        'crib' => array(
            'title' => 'Spick',
        ),
    ),
);
