# Changelog

##### Oktober 2022
- Die Druckfunktion bei Rückmeldungen wurde überarbeitet. Es werden nun direkt fertige, konsistent gelayoutete PDF-Dateien heruntergeladen, anstatt dass man im Browser die PDF-Druck-Funktion verwenden muss. Da wir nun nicht mehr vom Verhalten unterschiedlicher Browser abhängig sind, konnten zudem die Seitenränder auf dem PDF verkleinert werden, um Papier zu sparen [#228](https://github.com/gloggi/qualix/issues/228)
- Das Design des MiData-Login Buttons wurde ans neue PBS-Design angepasst. Merci @Sprudeel! [#265](https://github.com/gloggi/qualix/issues/265)
- Neue Links zum einfachen Weiterspringen auf nächste und vorhergehende TN auf der TN-Detailansicht. Merci @Sprudeel! [#274](https://github.com/gloggi/qualix/issues/274)
- Performance-Optimierungen für Qualix wurden ermöglicht. Merci @cleverer! [#44](https://github.com/gloggi/qualix/issues/44)
- Bugfixes: Wenn ein Feature (konkret MiData-Login und WebRTC Synchronisierung) in einer Instanz von Qualix nicht konfiguriert ist, dann wird es komplett ausgeblendet / deaktiviert. Merci @cleverer! [#286](https://github.com/gloggi/qualix/pull/286) [#284](https://github.com/gloggi/qualix/pull/284)

##### September 2022
- Die Ansichten "Blöcke" und "Spick" wurden kombiniert. Beobachtungsaufträge sind nun unter "Blöcke" verfügbar [#165](https://github.com/gloggi/qualix/issues/165)
- Unter "Rückmeldungen" im Hauptmenü kann jetzt eine Liste der (mir zugewiesenen) Rückmeldungen angezeigt werden [#259](https://github.com/gloggi/qualix/issues/259)
- Experimentell: Zu jeder Rückmeldung mit Mindestanforderungen kann eine Mindestanforderungs-Matrix angezeigt werden. Diese zeigt eine Übersicht über den Fortschritt aller TN in allen Mindestanforderungen, und erlaubt es den Erfüllungsstatus jeder Mindestanforderung zu kommentieren. Diese Kommentare sind auch im Rückmeldungs-Editor sichtbar, aber nicht wenn man die Rückmeldung ausdruckt [#267](https://github.com/gloggi/qualix/pull/267)
- Auf der Wilkommens-Seite gibt es nun einen Link zum Changelog [#220](https://github.com/gloggi/qualix/issues/220)

##### August 2022
- Es kann jetzt pro Kurs eingestellt werden, welchen Status die Anforderungen in einer Rückmeldung haben können. Die Stati können unter Kursadmin -> Anforderungen -> Stati verwalten... angepasst werden [#259](https://github.com/gloggi/qualix/issues/259)
- Die Icons wurden geupdatet. Für Anforderungs-Stati kann jetzt aus einer grösseren Auswahl an Icons gewählt werden. Falls dir ein Icon fehlt, melde dich beim Qualix-Team. Wir können jedes gewünschte Icon von [dieser Liste](https://fontawesome.com/search?m=free&s=solid) aktivieren [#264](https://github.com/gloggi/qualix/pull/264)

##### Juli 2022
- Qualis wurden zu Rückmeldungen umbenannt, um besser zu kommunizieren wofür sie gedacht sind, und damit klarer wird dass Beobachtungen nicht für die Erfassung von Rückmeldungen optimiert sind [#261](https://github.com/gloggi/qualix/issues/261) 

##### Juni 2022
- Upgrade auf Laravel 9 und PHP >= 8.0.2 [#254](https://github.com/gloggi/qualix/pull/254)
- Bugfix: Es tritt jetzt kein Fehler mehr auf wenn man einen Kurs archiviert oder löscht der TN ohne Bild enthält

##### Mai 2022
- Wenn mit dem Back-Button zurück auf eine Rückmeldung navigiert wird, wird jetzt der wirklich aktuellste Rückmeldungs-Inhalt angezeigt [#250](https://github.com/gloggi/qualix/issues/250)
- Beim Einfügen von Beobachtungen in eine Rückmeldung können jetzt optional diejenigen Beobachtungen herausgefiltert werden, die bereits in der Rückmeldung eingesetzt wurden (merci für die Idee @Tschet1) [#230](https://github.com/gloggi/qualix/issues/230)

##### April 2022
- Kontaktlink im Footer hinzugefügt [#233](https://github.com/gloggi/qualix/issues/233)
- Beim Eintragen von Beobachtungen wird das bisherige Zeichenlimit neu sichtbar gemacht [#223](https://github.com/gloggi/qualix/issues/223)
- Im Spick werden die Namen der Beobachtungsaufträge angezeigt, wenn man über ein TN-Bild fährt [#210](https://github.com/gloggi/qualix/issues/210)
- Nach dem Erstellen von Beobachtungen für mehrere TN gleichzeitig werden neu Links zu allen beobachteten TN angezeigt [#217](https://github.com/gloggi/qualix/issues/217)

##### März 2022
- Im Freitext auf TN können jetzt bis zu 65535 Zeichen eingetragen werden. Grund ist, dass in der MiData die TN-Empfehlung ebenfalls maximal so lange werden kann [#247](https://github.com/gloggi/qualix/issues/247)

##### Februar 2022
- Der TN-Import kommt jetzt besser damit zurecht, wenn Spalten fehlen oder in einer anderen Reihenfolge sind als erwartet, und gibt genauere Rückmeldungen was genau falsch ist.

##### Januar 2022
- Auf der Überblick-Seite können jetzt Rückmeldungen angezeigt werden [#242](https://github.com/gloggi/qualix/pull/242)
- Die Checkbox "Mindestanforderung" bei neuen Anforderungen ist standardmässig aktiviert, um harte Kriterien als best practice zu ermutigen.
- Security Updates

##### Dezember 2021
- Bugfix: Beobachtungen werden jetzt überall in der richtigen Reihenfolge nach Block sortiert, auch bei zweistelligen Blocknummern / Tagesnummern [#214](https://github.com/gloggi/qualix/issues/214)

##### November 2021
- Bugfix bei der Equipe: Einladungen können jetzt wieder gelöscht werden (merci für die Meldung @mario-zelger) [#232](https://github.com/gloggi/qualix/issues/232)

##### Oktober 2021
- Bugfix im Rückmeldungs-Editor: In Rückmeldungen welche keine Anforderungen enthalten wird der Editor-Inhalt beim erneuten Öffnen nicht mehr dupliziert (merci für die Meldung @Tschet1) [#223](https://github.com/gloggi/qualix/issues/229)

##### September 2021
- Die Inhalte des Rückmeldungs-Editors werden jetzt automatisch gespeichert, sobald 2 Sekunden lang keine Eingabe getätigt wird. Damit das nicht zu Konflikten mit anderen Nutzenden führt, wird ausserdem der Inhalt des Rückmeldungs-Editors zwischen allen Browsern synchronisiert, welche dieselbe Rückmeldung offen haben. Die Synchronisierung läuft über eine end-to-end-verschlüsselte Direktverbindung zwischen den Browsern [#221](https://github.com/gloggi/qualix/issues/221)

##### August 2021
- Die Profilbilder der Equipenmitglieder werden jetzt im Kursadministrationsbereich angezeigt

##### Juni 2021
- Updates auf https://qualix.flamberg.ch werden jetzt, ausser in Notfällen, jeweils nur noch in der Nacht eingespielt, damit laufende Kurse nicht tangiert werden [#149](https://github.com/gloggi/qualix/issues/149)

##### Mai 2021
- Fix: Der Login via hitobito klappt jetzt auch, wenn man auf der MiData keinen Pfadinamen eingetragen hat [#199](https://github.com/gloggi/qualix/pull/199)

##### April 2021
- Alle TN haben jetzt ein optionales Freitextfeld, das z.B. für Förderpunkte eingesetzt werden kann. Der Freitext wird auf der TN-Detailseite angezeigt. [#191](https://github.com/gloggi/qualix/pull/191)
- Die Imports von Blöcken und TN akzeptieren jetzt eine breitere Pallette an Dateiformaten. Bei CSV wird das Encoding automatisch erraten, sodass Umlaute und andere Sonderzeichen zuverlässiger importiert werden. [#192](https://github.com/gloggi/qualix/pull/192)

##### März 2021
- Der subjektive Eindruck auf Beobachtungen kann in den Kurseinstellungen deaktiviert werden. Optionale Felder (Eindruck, Anforderungen, Kategorien) werden nicht mehr angezeigt, wenn sie im Kurs nicht eingesetzt werden. [#189](https://github.com/gloggi/qualix/pull/189)

##### Februar 2021
- Neu kann eine Rückmeldung eines TN mehreren Equipenmitgliedern zugewiesen werden [#178](https://github.com/gloggi/qualix/pull/178)
- Diverse Verbesserungen am Rückmeldungs-Editor [#186](https://github.com/gloggi/qualix/pull/186)
- Die Blöcke in der Auswahl beim Beobachtung Erfassen werden jetzt nach unten geschoben, wenn sie älter als vom Vortag sind [#188](https://github.com/gloggi/qualix/pull/188)

##### Januar 2021
- Grenzwerte für die roten und grünen Markierungen sind jetzt pro Kurs einstellbar [#173](https://github.com/gloggi/qualix/pull/173)

##### Dezember 2020
- Changelog eingeführt [#167](https://github.com/gloggi/qualix/pull/167)

##### Oktober 2020
- Beobachtungsaufträge [#147](https://github.com/gloggi/qualix/pull/147)
- Rückmeldungen erfassen und ausdrucken [#146](https://github.com/gloggi/qualix/pull/146)

##### September 2020
- Security Updates [#143](https://github.com/gloggi/qualix/pull/143) [#144](https://github.com/gloggi/qualix/pull/144)
- TN-Gruppen [#142](https://github.com/gloggi/qualix/pull/142)
- Security-Verbesserungen [#140](https://github.com/gloggi/qualix/pull/140)

##### August 2020
- Falschen Titel beim Beobachtung bearbeiten korrigiert (danke @diegosteiner) [#139](https://github.com/gloggi/qualix/pull/139)

##### Juli 2020
- Upgrade auf Laravel 7 und PHP >= 7.2.5 [#137](https://github.com/gloggi/qualix/pull/137)

##### Juni 2020
- TN von MiData-Liste importieren [#136](https://github.com/gloggi/qualix/pull/136)

##### April 2020
- Beim Erstellen von Anforderungen können direkt Blöcke verknüpft werden [#125](https://github.com/gloggi/qualix/pull/125)

##### März 2020
- Security Updates [#126](https://github.com/gloggi/qualix/pull/126) [#127](https://github.com/gloggi/qualix/pull/127) [#128](https://github.com/gloggi/qualix/pull/128)
- Killerkriterien zu Mindestanforderungen umbenannt [#123](https://github.com/gloggi/qualix/pull/123)

##### Februar 2020
- Die aktuellsten Features von Qualix werden automatisch auf https://qualix.flamberg.ch ausgerollt [#113](https://github.com/gloggi/qualix/pull/113)

##### Januar 2020
- Multi TN Beobachtungen [#106](https://github.com/gloggi/qualix/pull/106)
- Import von Blöcken aus eCamp v2 [#107](https://github.com/gloggi/qualix/pull/107)
- Wiederherstellung von Formulareingaben nach automatischem Logout wegen Timeout [#105](https://github.com/gloggi/qualix/pull/105)

##### Dezember 2019
- Übersetzung auf französisch [#99](https://github.com/gloggi/qualix/pull/99)
- Security Updates [#100](https://github.com/gloggi/qualix/pull/100) [#101](https://github.com/gloggi/qualix/pull/101)

##### November 2019
- MIT-Lizenz für die Software eingeführt [#90](https://github.com/gloggi/qualix/pull/90)

##### Oktober 2019
- Security-Fix [#89](https://github.com/gloggi/qualix/pull/89)

##### September 2019
- Tagesspick [#88](https://github.com/gloggi/qualix/pull/88)
- MiData-Login [#87](https://github.com/gloggi/qualix/pull/87)

##### Juli 2019
- Hilfetexte an verschiedenen Stellen in der Benutzeroberfläche [#85](https://github.com/gloggi/qualix/pull/85)

##### Juni 2019
- Das erste oder wichtigste Formularfeld wird automatisch ausgewählt [#81](https://github.com/gloggi/qualix/pull/81)
- Archivierung und Löschung von Kursen [#82](https://github.com/gloggi/qualix/pull/82)

##### April 2019
- Release von Qualix als Neuimplementation des Quali-Tools des Flamberg [#30](https://github.com/gloggi/qualix/pull/30)
