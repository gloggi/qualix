#!/bin/bash

composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

if [[ ! -f .env ]]; then
    cp .env.example .env
    while [ ! -f .env ]; do
        sleep 1
    done
    php artisan key:generate
    php artisan config:cache
fi

chmod 777 -R storage bootstrap/cache

php artisan db:wait
php artisan migrate

touch .ready

apache2-foreground
