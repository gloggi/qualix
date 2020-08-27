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
docker exec -it qualix-app composer update
docker exec -it qualix-app php artisan tinker
docker exec -it qualix-app php -v
```

## Produktive Installation

> Qualix basiert auf Laravel 7 und benötigt deshalb eine PHP Version >= 7.2.5

1. **Code herunterladen**: `git clone https://github.com/gloggi/qualix.git && cd qualix`
2. **Server-Einstellungen**: Eine Kopie von .env.example namens .env erstellen und die Angaben darin ergänzen. Zum Beispiel so (kritische Angaben sind mit `<snip>` zensiert):
```
APP_NAME=Qualix
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://qualix.flamberg.ch

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

SENTRY_LARAVEL_DSN=<snip>
SENTRY_USER_FEEDBACK_URL=<snip>
SENTRY_CSP_REPORT_URI=<snip>
```
3. **Backend-Dependencies installieren und `APP_KEY` generieren**: `docker-compose run --entrypoint "/bin/sh -c 'composer install --no-dev && php artisan key:generate'" app`
4. **Frontend-Code builden**: `docker-compose run --entrypoint "/bin/sh -c 'npm install && npm run prod'" node`
5. **Optimierung (optional)**: `docker-compose run --entrypoint "composer install --optimize-autoloader --no-dev" app`
6. **Auf den Webhost hochladen**: Z.B. mit FTP alles (Ordner und Dateien) ausser .git, node_modules und tests hochladen
7. **Mit SSH auf den Server einloggen**, da die folgenden Befehle in der finalen Umgebung ausgeführt werden müssen
8. **Optimierung (optional)**: `php artisan config:cache && php artisan route:cache`
9. **Datenbank-Tabellen einrichten**: `php artisan migrate`
10. **Laravel Storage-Link einrichten**: `php artisan storage:link`
