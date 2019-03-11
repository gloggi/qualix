# Qualix
> was gaffsch?

Webapp zur Erfassung und Verwaltung von qualifikationsrelevanten Beobachtungen in J+S-Ausbildungskursen der Pfadi.

Eine Live-Version wird unter <https://qualix.flamberg.ch> verfügbar sein.

## Lokale Installation

Qualix ist ein PHP-Projekt basierend auf dem Framework Laravel. Wir verwenden [docker-compose](https://docs.docker.com/compose/install/) um es lokal zur Entwicklung laufen zu lassen. Wenn du auf Linux arbeitest, musst du zuerst noch separat [Docker für Linux](https://docs.docker.com/install/#server) installieren.

Um Qualix laufen zu lassen, musst du nur den Quellcode mit Git herunterladen und die Container starten:
```
git clone https://github.com/gloggi/qualix.git
cd qualix
docker-compose up
```

Wenn sich der Output beruhigt hat (wenn im Output zuletzt etwas wie `qualix-app | [...] [core:notice] [pid ...] AH00094: Command line: 'apache2 -D FOREGROUND'` steht), kannst du dein lokales Qualix unter <http://localhost> und das Datenbank-Tool phpMyAdmin unter <http://localhost:8081> aufrufen.

## Composer, artisan, etc. im Container

Da alles was mit Qualix zu tun hat, inklusive PHP, Composer, artisan, etc. nur im Container läuft, musst du entsprechende Befehle auch in den Container hinein absetzen. Hier ein Paar Beispiele:

```
docker exec -it qualix-app composer update
docker exec -it qualix-app php artisan tinker
docker exec -it qualix-app php -v
```