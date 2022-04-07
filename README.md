# Qualix
> was gaffsch?

Webapp zur Erfassung und Verwaltung von qualifikationsrelevanten Beobachtungen in J+S-Ausbildungskursen der Pfadi.

Eine Live-Version ist unter <https://qualix.flamberg.ch> verfügbar.

Wir benützen [Phrase](https://phrase.com), um Qualix auch in anderen Sprachen anzubieten.

## Lokale Installation

Qualix ist ein PHP-Projekt basierend auf dem Framework Laravel. Wir verwenden [docker-compose](https://docs.docker.com/compose/install/) um es lokal zur Entwicklung laufen zu lassen. Wenn du auf Linux arbeitest, musst du zuerst noch separat [Docker für Linux](https://docs.docker.com/install/#server) installieren.

Um Qualix laufen zu lassen, musst du nur den Quellcode mit Git herunterladen und die Container starten:
```
git clone https://github.com/gloggi/qualix.git
cd qualix
docker-compose up
```

Wenn sich der Output beruhigt hat (wenn im Output mehrere Zeilen mit `qualix-npm` und `[emitted]` stehen), kannst du dein lokales Qualix unter <http://localhost> und das Datenbank-Tool phpMyAdmin unter <http://localhost:8081> aufrufen.

Wenn du E-Mails in deiner Qualix-Kopie auslöst (zum Beispiel beim Passwort-Reset oder wenn du jemanden in einen Kurs einlädst), werden diese nicht wirklich abgesendet, sondern sie landen im Mailcatcher. Du kannst diese Mails unter <http://localhost:1080> ansehen.

### Composer, artisan, etc. im Container

Da alles was mit Qualix zu tun hat, inklusive PHP, Composer, artisan, etc. nur im Container läuft, musst du entsprechende Befehle auch in den Container hinein absetzen. Hier ein Paar Beispiele:

```
docker-compose exec qualix composer update
docker-compose exec qualix php artisan tinker
docker-compose exec qualix php -v
```

### Tests laufen lassen

Für sämtliche Tests müssen zuerst die Container laufen (`docker-compose up`).

#### PHP Unit Tests

Vor den Unit Tests musst du einmalig die gecachete Config löschen:
```
docker-compose exec qualix php artisan config:clear --env=testing
```

Danach kannst du die Unit Tests mit PHPUnit laufen lassen:
```
# Alle Unit Tests
docker-compose exec qualix vendor/bin/phpunit

# Nur Unit Tests die "Course" im Namen haben
docker-compose exec qualix vendor/bin/phpunit --filter=Course
```

#### Frontend Tests

Die Frontend Tests kannst du wie folgt laufen lassen:
```
# Alle Frontend Tests
docker-compose exec node npm run test

# Ein spezifischer Test
docker-compose exec node npm run test -- ObservationList
```

#### End-to-end Tests

Du kannst die E2E-Tests mit Cypress headless (ohne sichtbares Browserfenster) laufen lassen:
```
docker-compose run e2e run
```

Oder du kannst das grafische Tool von Cypress vom Container aus auf deinem Mac- oder Linux-Computer öffnen:
```
# Nur auf Mac OS: xhost installieren, danach den Computer neu starten
brew cask install xquartz

# Dem Container erlauben, auf dem Host Fenster zu öffnen (nur einmal pro Computer-Neustart nötig)
xhost local:root

# Cypress öffnen
docker-compose run e2e open
```

## Produktive Installation

> Qualix basiert auf Laravel 8 und benötigt deshalb eine PHP Version >= 7.4

1. **Code herunterladen**: `git clone https://github.com/gloggi/qualix.git && cd qualix`
2. **Server-Einstellungen**: Eine Kopie von .env.example namens .env erstellen und die Angaben darin ergänzen. Zum Beispiel so (kritische Angaben sind mit `<snip>` zensiert):
```
APP_NAME=Qualix
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://qualix.flamberg.ch
# Set your own contact
#APP_CONTACT_LINK=mailto:info@qualix.flamberg.ch
#APP_CONTACT_TEXT="Kontaktiere Qualix"

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=<snip>
DB_PORT=3306
DB_DATABASE=<snip>
DB_USERNAME=<snip>
DB_PASSWORD=<snip>

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=<snip>
MAIL_PORT=<snip>
MAIL_USERNAME=<snip>
MAIL_PASSWORD=<snip>
MAIL_ENCRYPTION=SSL

HITOBITO_BASE_URL=https://db.scout.ch
HITOBITO_CLIENT_UID=<snip>
HITOBITO_CLIENT_SECRET=<snip>
HITOBITO_CALLBACK_URI=https://qualix.flamberg.ch/login/hitobito/callback

# Collaboration in the quali editor via WebRTC
COLLABORATION_ENABLED=true
COLLABORATION_SIGNALING_SERVERS="wss://signaling.yjs.dev wss://y-webrtc-signaling-eu.herokuapp.com wss://y-webrtc-signaling-us.herokuapp.com"

SENTRY_LARAVEL_DSN=<snip>
SENTRY_USER_FEEDBACK_URL=<snip>
SENTRY_CSP_REPORT_URI=<snip>
MIX_SENTRY_VUE_DSN=<snip>
```
3. **Backend-Dependencies installieren und `APP_KEY` generieren**: `docker-compose run --entrypoint "/bin/sh -c 'composer install --no-dev && php artisan key:generate'" qualix`
4. **Frontend-Code builden**: `docker-compose run --entrypoint "/bin/sh -c 'npm install && npm run prod'" node`
5. **Optimierung (optional)**: `docker-compose run --entrypoint "composer install --optimize-autoloader --no-dev" qualix`
6. **Auf den Webhost hochladen**: Z.B. mit FTP alles (Ordner und Dateien) ausser .git, cypress, node_modules und tests hochladen
7. **Mit SSH auf den Server einloggen**, da die folgenden Befehle in der finalen Umgebung ausgeführt werden müssen
8. **Optimierung (optional)**: `php artisan config:cache && php artisan route:cache`
9. **Datenbank-Tabellen einrichten**: `php artisan migrate`
10. **Laravel Storage-Link einrichten**: `php artisan storage:link`
