<?php
return array(
	"errors" => array(
		"form_data_restored_please_submit_again" => "Deine eingegebenen Daten wurden wiederhergestellt. Speichern nicht vergessen!",
		"session_expired_try_again" => "Ups, du bist inzwischen nicht mehr eingeloggt. Bitte logge dich nochmals ein, deine Eingaben werden dann wiederhergestellt.",
	),
	"footer" => array(
		"slogan" => "Qualix. was gaffsch?",
	),
	"global" => array(
		"add" => "Hinzufügen",
		"add_observation" => "Beobachtung erfassen",
		"close" => "Schliessen",
		"delete" => "Löschen",
		"edit" => "Bearbeiten",
		"negative" => "Negativ",
		"neutral" => "Neutral",
		"no" => "Nein",
		"no_options" => "Keine Einträge gefunden",
		"or" => "oder",
		"page_title" => "Qualix",
		"positive" => "Positiv",
		"really_delete" => "Wirklich löschen?",
		"save" => "Speichern",
		"total" => "Total",
		"yes" => "Ja",
	),
	"header" => array(
		"archived" => "Archiviert",
		"course_admin" => "Kursadmin",
		"language_switch" => "Sprache wechseln. Übersetzungen via Phrase.com",
		"qualix" => "Qualix",
		"welcome" => "Willkommen, :user",
	),
	"mails" => array(
		"invitation" => array(
			"accept" => "Klicke :here um die Einladung anzunehmen.",
			"greeting" => "Liebe Grüsse, dein Qualix-Team",
			"here" => "hier",
			"subject" => "Qualix: Einladung in \":courseName\"",
			"you_have_been_invited" => ":inviterName hat dich auf Qualix in den Kurs \":courseName\" eingeladen.",
		),
	),
	"models" => array(
		"block" => array(
			"block_date" => "Datum",
			"full_block_number" => "Blocknummer",
			"name" => "Blockname",
			"num_observations" => "Anzahl Beobachtungen",
			"requirements" => "Anforderungen",
		),
		"category" => array(
			"name" => "Titel",
			"num_observations" => "Anzahl Beobachtungen",
		),
		"course" => array(
			"course_number" => "Kursnummer",
			"name" => "Kursname",
		),
		"invitation" => array(
			"email" => "E-Mail",
		),
		"invitation_claim" => array(
			"token" => "Token",
		),
		"observation" => array(
			"block" => "Block",
			"categories" => "Kategorien",
			"content" => "Beobachtung",
			"impression" => "Eindruck",
			"participant" => "TN",
			"participants" => "TN",
			"requirements" => "Anforderungen",
			"user" => "Beobachter",
		),
		"participant" => array(
			"group" => "Abteilung",
			"image" => "Bild",
			"scout_name" => "Pfadiname",
		),
		"quali" => array(
			"name" => "Titel",
			"participant" => "TN",
			"participants" => "TN",
			"requirement_progress" => "Anforderungen",
			"requirements" => "Relevante Anforderungen",
			"user" => "Zuständig",
		),
		"requirement" => array(
			"blocks" => "Blöcke",
			"content" => "Anforderung",
			"mandatory" => "Mindestanforderung",
			"num_observations" => "Anzahl Beobachtungen",
		),
		"user" => array(
			"email" => "E-Mail",
			"group" => "Abteilung",
			"image" => "Bild",
			"name" => "Name",
			"password" => "Passwort",
			"password_confirmation" => "Passwort bestätigen",
		),
	),
	"views" => array(
		"admin" => array(
			"blocks" => array(
				"are_blocks_required" => array(
					"answer" => "Ja, jede Beobachtung gehört zu genau einem Block. Daher kannst du Qualix nur verwenden, wenn du Blöcke im Kurs erfasst hast. Falls du Beobachtungen ausserhalb der Blöcke machen willst, empfehlen wir, einen oder mehrere \"Sonstiges\"-Blöcke zu erfassen.",
					"question" => "Muss ich Blöcke für meinen Kurs erfassen?",
				),
				"create_success" => "Block \":name\" wurde erfolgreich erstellt.",
				"delete_success" => "Block \":name\" wurde erfolgreich gelöscht.",
				"edit" => "Block bearbeiten",
				"edit_success" => "Block \":name\" wurde erfolgreich gespeichert.",
				"existing" => "Blöcke :courseName",
				"import" => "Blöcke importieren...",
				"menu_name" => "Blöcke",
				"new" => "Neuer Block",
				"no_blocks" => "Bisher sind keine Blöcke erfasst.",
				"observations_on_block" => "{0}Es ist keine Beobachtung damit verbunden.|{1}Damit verbunden ist eine Beobachtung, die mitgelöscht wird.|[2,*]Damit verbunden sind :count Beobachtungen, die mitgelöscht werden.",
				"really_delete" => "Willst du den Block \":name\" wirklich löschen?",
				"what_are_blocks" => array(
					"answer" => "Blöcke sind zeitliche Abschnitte im Grobprogramm. Man könnte sie auch Lektionen oder Programmeinheiten nennen. Du kannst zudem erfassen, welche Anforderungen in einem Block wohl am ehesten beobachtet werden können (z.B. eine Anforderung zu Sicherheitsüberlegungen in einem Block über Sicherheitskonzepte). Beim Erfassen von Beobachtungen kann das aber immer noch übersteuert werden.",
					"question" => "Was sind Blöcke?",
				),
			),
			"block_import" => array(
				"ecamp2" => array(
					"block_overview" => "Blockübersicht",
					"how_to_get_the_block_overview" => array(
						"answer" => "Wenn du die Blöcke in deinem Kurs auf :ecamp2 erfasst hast, gehe dort links in der Navigation zu \"Kurs Ziele\". Danach kannst du die Blockübersicht oben rechts als Excel-Datei herunterladen.",
						"question" => "Woher bekomme ich die Blockübersicht?",
					),
					"name" => "eCamp v2",
				),
				"error_while_parsing" => "Die Blockübersicht konnte nicht korrekt gelesen werden - hast du die Datei unverändert hochgeladen?",
				"import" => "Importieren",
				"import_from" => "Blöcke aus :source importieren",
				"import_success" => "{0}In der importierten Datei wurden keine Blöcke gefunden.|{1}In der importierten Datei wurde ein Block gefunden.|[2,*]In der importierten Datei wurden :count Blöcke gefunden.",
				"unknown_error" => "Beim Import ist ein Fehler aufgetreten. Versuche es nochmals, oder erfasse deine Blöcke manuell.",
				"warning_existing_blocks" => "In deinem Kurs sind bereits Blöcke definiert. Wenn beim Import eine Blocknummer schon existiert, wird der bestehende Block durch den Import aktualisiert.",
			),
			"categories" => array(
				"are_categories_required" => array(
					"answer" => "Nein, Kategorien sind komplett optional, falls ihr in eurem Kursteam keine Verwendung dafür habt.",
					"question" => "Muss ich Kategorien für meinen Kurs erfassen?",
				),
				"create_success" => "Kategorie \":name\" wurde erfolgreich erstellt.",
				"delete_success" => "Kategorie \":name\" wurde erfolgreich gelöscht.",
				"edit" => "Kategorie bearbeiten",
				"edit_success" => "Kategorie \":name\" wurde erfolgreich gespeichert.",
				"existing" => "Kategorien :courseName",
				"menu_name" => "Kategorien",
				"new" => "Neue Kategorie",
				"no_categories" => "Bisher sind keine Kategorien erfasst.",
				"observations_on_category" => "{0}Es ist keine Beobachtung damit verbunden.|{1}Damit verbunden ist eine Beobachtung, diese wird aber nicht gelöscht.|[2,*]Damit verbunden sind :count Beobachtungen, diese werden aber nicht gelöscht.",
				"really_delete" => "Willst du die Kategorie \":name\" wirklich löschen?",
				"what_are_categories" => array(
					"answer" => "Kategorien können auf verschiedene Art eingesetzt werden. Jeder Beobachtung kann eine, mehrere oder keine Kategorie zugewiesen werden. Das kann man zum Beispiel zur Einordnung in verschiedene Abschnitte eines Quali-Formulars verwenden. Oder um zu markieren, ob eine Beobachtung schon im Zwischenquali angesprochen wurde. Oder noch ganz andere Anwendungen, die dir einfallen. Danach kannst du die Beobachtungs-Liste eines Teilnehmenden nach Kategorien filtern.",
					"question" => "Was sind Kategorien?",
				),
			),
			"course_settings" => array(
				"archive" => "Kurs archivieren…",
				"archive_confirm" => "Definitiv archivieren",
				"archive_description" => "Dies wird alle TN und Beobachtungen im Kurs komplett und dauerhaft löschen. Diese Aktion kann nicht rückgängig gemacht werden. Blöcke, Anforderungen, Kategorien und Equipenmitglieder bleiben zur späteren Einsicht bestehen.",
				"archive_or_delete" => "Kurs archivieren oder löschen",
				"archive_success" => "Kurs \":name\" wurde archiviert.",
				"archive_vs_delete" => array(
					"answer" => "Wenn du einen Kurs archivierst, werden alle personenbezogenen Daten (TN, Bilder, Beobachtungen) dauerhaft gelöscht. So kannst du Datenschutz-Problemen entgegenwirken, aber für spätere Kurse trotzdem noch deine alten Anforderungen und Blöcke einsehen. Wenn du den Kurs hingegen ganz löschst wird alles was damit zu tun hat unwiderruflich entfernt.",
					"question" => "Was ist der Unterschied?",
				),
				"delete" => "Kurs komplett löschen…",
				"delete_confirm" => "Definitiv löschen",
				"delete_description" => "Dies wird den Kurs komplett und dauerhaft löschen, inklusive alle Blöcke, TN, Anforderungen, Kategorien, Teilnehmer und Beobachtungen darin. Diese Aktion kann nicht rückgängig gemacht werden.",
				"delete_success" => "Kurs \":name\" und alle damit verbundenen Daten wurden gelöscht.",
				"edit" => "Kurseinstellungen :name",
				"edit_success" => "Kursdetails erfolgreich gespeichert.",
				"is_archived" => ":name ist archiviert, das heisst alle personenbezogenen Daten der Teilnehmenden wurden gelöscht.",
				"menu_name" => "Kurseinstellungen",
				"really_archive" => "Kurs \":name\" wirklich archivieren?",
				"really_delete" => "Kurs \":name\" wirklich löschen?",
			),
			"equipe" => array(
				"cannot_delete_last_leiter" => "Mindestens ein Equipenmitglied muss im Kurs bleiben.",
				"delete_invitation_success" => "Die Einladung für :email wurde erfolgreich gelöscht.",
				"delete_success" => "Leiterrole erfolgreich entfernt.",
				"existing" => "Equipe :courseName",
				"existing_invitations" => "Einladungen",
				"invitation_email_sent" => "Wir haben eine Einladung an :email gesendet.",
				"invite" => "Einladen",
				"menu_name" => "Equipe",
				"new_invitation" => "Equipenmitglied einladen",
				"no_invitations" => "Momentan sind keine Einladungen offen.",
				"really_delete" => "Willst du :name wirklich aus der Kursequipe entfernen?",
				"really_delete_invitation" => "Willst du die Einladung für :email wirklich entfernen?",
			),
			"new_course" => array(
				"create" => "Kurs eröffnen",
				"create_success" => "Kurs \":name\" wurde erfolgreich erstellt.",
				"menu_name" => "Neuen Kurs erstellen",
				"title" => "Neuen Kurs erstellen",
			),
			"participants" => array(
				"add_success" => "TN \":name\" erfolgreich erfasst.",
				"edit" => "TN ändern",
				"edit_success" => "TN \":name\" erfolgreich gespeichert.",
				"existing" => "Teilnehmende :courseName",
				"menu_name" => "TN",
				"new" => "Neue Teilnehmende",
				"no_participants" => "Bisher sind keine Teilnehmende erfasst.",
				"observations_on_participant" => "{0}Es ist keine Beobachtung damit verbunden.|{1}Damit verbunden ist eine Beobachtung, die mitgelöscht wird.|[2,*]Damit verbunden sind :count Beobachtungen, die mitgelöscht werden.",
				"really_remove" => "Willst du :name wirklich aus deinem Kurs entfernen?",
				"remove_success" => "TN \":name\" erfolgreich aus dem Kurs entfernt.",
			),
			"qualis" => array(
				"back_to_quali_list" => "zurück zur Liste der Qualis",
				"create" => "Erstellen",
				"go_back_to_quali_list" => "Zurück zur Liste der Qualis",
				"create_success" => "Das Quali \":name\" wurde erfolgreich erstellt. Du kannst nun noch die einzelnen TN-Qualis den Equipenmitgliedern zuweisen. Falls du das jetzt nicht machen möchtest, gehts hier :back_to_quali_list",
				"delete_success" => "Das Quali \":name\" wurde erfolgreich gelöscht.",
				"edit" => "Quali bearbeiten",
				"edit_success" => "Das Quali \":name\" wurde erfolgreich gespeichert.",
				"existing" => "Qualis :courseName",
				"leader_assignment" => "Zuordnung der TN zu den Equipenmitgliedern",
				"menu_name" => "Qualis",
				"new" => "Neues Quali",
				"no_qualis" => "Bisher sind keine Qualis erfasst.",
				"select_all_participants" => "Alle auswählen",
				"select_all_requirements" => "Alle auswählen",
				"quali_notes_template" => "Vorlage für Quali-Text",
				"quali_notes_template_description" => array(
					"answer" => "Der Quali-Text wird bei allen TN vorausgefüllt mit dem Text den du hier eingibst. So kannst du zum Beispiel vorgeben, welche Abschnitte jedes Quali haben sollte. Aber Achtung: Nachdem das Quali erstellt ist kannst du den Quali-Text nur noch ändern indem du ihn bei allen TN einzeln von Hand bearbeiten!",
					"question" => "Wofür ist dieses Feld?",
				),
				"really_delete" => "Willst du das Quali \":name\" wirklich löschen? Alle Informationen über bestandene Anforderungen werden dabei mitgelöscht. Die Beobachtungen die damit verbunden waren bleiben aber erhalten.",
				"what_are_qualis" => array(
					"answer" => "Ein Quali steht für ein TN-Gespräch, Zwischengespräch oder eine Qualifikation die alle Teilnehmenden separat bestehen können. Auf der Detailansicht der ausgewählten TN kannst du später einen Quali-Text zusammenstellen. Für ein Quali können alle Anforderungen des Kurses oder auch nur ein Teil davon relevant sein.",
					"question" => "Was sind Qualis?",
				),
			),
			"requirements" => array(
				"are_requirements_required" => array(
					"answer" => "Es ist sehr wichtig, vor dem Kurs im Kursteam Anforderungen festzulegen, damit alle Teilnehmenden nach dem gleichen Schema qualifiziert werden und damit Entscheide im Kurs einfacher gefällt werden können. Aber wenn du diese nicht in Qualix führen willst, kannst du Beobachtungen auch ohne Anforderungen erfassen.",
					"question" => "Muss ich Anforderungen für meinen Kurs erfassen?",
				),
				"create_success" => "Anforderung wurde erfolgreich erstellt.",
				"delete_success" => "Anforderung wurde erfolgreich gelöscht.",
				"edit" => "Anforderung bearbeiten",
				"edit_success" => "Anforderung wurde erfolgreich gespeichert.",
				"existing" => "Anforderungen :courseName",
				"menu_name" => "Anforderungen",
				"new" => "Neue Anforderung",
				"no_requirements" => "Bisher sind keine Anforderungen erfasst.",
				"observations_on_requirement" => "{0}Es ist keine Beobachtung damit verbunden.|{1}Damit verbunden ist eine Beobachtung, diese wird aber nicht gelöscht.|[2,*]Damit verbunden sind :count Beobachtungen, diese werden aber nicht gelöscht.",
				"really_delete" => "Willst du diese Anforderung wirklich löschen?",
				"what_are_requirements" => array(
					"answer" => "Anforderungen sind klare Voraussetzungen und Kriterien, die alle Teilnehmenden während dem Kurs erfüllen sollen. Anhand der Anforderungen wird beurteilt, wer den Kurs besteht und wer nicht. Du kannst Anforderungen als Mindestanforderungen markieren wenn du willst, aber es hat momentan keine Auswirkungen in Qualix selber (bis auf eine etwas andere Farbgebung).",
					"question" => "Was sind Anforderungen?",
				),
			),
		),
		"blocks" => array(
			"here" => "hier",
			"menu_name" => "Blöcke",
			"no_blocks" => "Bisher sind keine Blöcke erfasst. Bitte erfasse sie :here.",
			"title" => "Beobachtung in Block erfassen",
		),
		"crib" => array(
			"here" => "hier",
			"mandatory_requirements" => "Mindestanforderungen",
			"menu_name" => "Spick",
			"non_mandatory_requirements" => "Weitere Anforderungen",
			"no_blocks" => "Bisher sind keine Blöcke erfasst. Bitte erfasse und verbinde sie :here mit Anforderungen.",
			"see_only_empty_blocks" => array(
				"answer" => "Dann sind bisher keine Blöcke mit Anforderungen verbunden. Bitte verbinde die Blöcke :here mit Anforderungen.",
				"question" => "Siehst du nur leere Blöcke ohne Anforderungen?",
			),
			"title" => "Welche Anforderungen können in welchen Blöcken beobachtet werden",
		),
		"error_form" => array(
			"back" => "Zurück zu wo ich gerade noch war...",
			"back_without_sending_report" => "Zurück ohne eine Beschreibung zu senden",
			"error_report_has_been_submitted" => "Deine Beschreibung wurde abgesendet. Vielen Dank!",
			"it_looks_like_we_are_having_issues" => "Es sieht so aus als hätten wir ein Problem.",
			"our_team_has_been_notified" => "Das Qualix-Team wurde bereits informiert. Wenn du uns helfen möchtest, teile uns bitte unten mit, was geschehen ist.",
			"please_try_again_later" => "Bitte versuche es später nochmals.",
			"send_description" => "Beschreibung absenden",
			"thank_you" => "Danke",
			"what_happened" => "Was ist passiert?",
			"what_happened_example" => "Ich habe auf das \"X\" und dann auf \"Bestätigen\" geklickt.",
		),
		"invitation" => array(
			"accept_invitation" => "Ja, Einladung annehmen",
			"accept_success" => "Einladung angenommen. Du bist jetzt in der Kursequipe von \":courseName\".",
			"already_in_equipe" => "Du bist schon in der Equipe von :courseName. Du kannst diese Einladung nicht annehmen.",
			"decline_invitation" => "Nein, diese Einladung ist nicht für mich",
			"error" => "Einladung konnte nicht angenommen werden.",
			"is_email_yours" => "Gehört dir die Mailadresse :email?",
			"title" => "Einladung in :courseName",
		),
		"login" => array(
			"midata" => array(
				"error_please_retry" => "Etwas hat nicht geklappt. Versuche es noch einmal.",
				"error_retry_later" => "Leider klappt es momentan gerade nicht. Versuche es später wieder, oder registriere dich mit einem klassischen Account.",
				"user_has_denied_access" => "Zugriff in MiData verweigert.",
				"use_normal_credentials" => "Melde dich bitte wie üblich mit Benutzernamen und Passwort an.",
			),
			"via_midata" => "Via PBS MiData einloggen",
		),
		"observations" => array(
			"add_success" => "Beobachtung erfasst. Mässi!",
			"back_to_participant" => "Zurück zu :name",
			"edit" => "Beobachtung bearbeiten",
			"edit_success" => "Beobachtung aktualisiert.",
			"new" => "Beobachtung erfassen",
		),
		"overview" => array(
			"here" => "hier",
			"menu_name" => "Überblick",
			"no_participants" => "Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie :here.",
			"title" => "Beobachtungs-Überblick",
		),
		"participants" => array(
			"here" => "hier",
			"menu_name" => "TN",
			"no_participants" => "Bisher sind keine Teilnehmende erfasst. Bitte erfasse sie :here.",
			"title" => "Beobachtung für TN erfassen",
		),
		"participant_details" => array(
			"delete_observation_success" => "Beobachtung gelöscht.",
			"existing_observations" => "Beobachtungen",
			"filter" => "Filter",
			"filter_by_category" => "Kategorie",
			"filter_by_requirement" => "Anforderung",
			"no_observations" => "Keine Beobachtungen gefunden.",
			"num_observations" => "{0}Bisher keine Beobachtungen.|{1}Erst eine Beobachtung. Da geht noch mehr!|[2,*]:count Beobachtungen, davon :positive mit positivem, :neutral mit neutralem und :negative mit negativem Eindruck.",
			"observations_without_category" => "Beobachtungen ohne Kategorie",
			"observations_without_requirement" => "Beobachtungen ohne Anforderung",
			"qualis" => array(
				"requirements_failed" => ":count nicht bestanden",
				"requirements_passed" => ":count bestanden",
				"title" => "Qualis",
			),
			"really_delete_observation" => "Willst du diese Beobachtung wirklich löschen?",
			"title" => "TN Details",
		),
		"quali_content" => array(
			"back_to_participant" => "Zurück zu :name",
			"error_requirements_changed" => "Die Änderungen konnten nicht gespeichert werden, weil die Anforderungen im Quali inzwischen geändert wurden.",
			"participant_quali" => ":quali: :participant",
			"requirements_status" => "Anforderungen",
			"title" => "Quali Details",
		),
		"register" => array(
			"via_midata" => "Via PBS MiData registrieren",
		),
		"user_settings" => array(
			"edit" => "Mein Profil bearbeiten",
			"edit_success" => "Die persönlichen Details wurden erfolgreich gespeichert.",
		),
		"welcome" => array(
			"no_courses" => "Du bist momentan noch in keinem Kurs eingetragen. Lass dich in einen Kurs einladen oder erstelle selber einen neuen.",
			"text" => "Qualix soll gegen den Papier-Krieg helfen und euch dabei unterstützen, den Überblick über alle Beobachtungen zu behalten. Viel Spass beim Beobachten!",
			"title" => "Willkommä bim Qualix",
		),
	),
);