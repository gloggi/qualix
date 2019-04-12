#!/bin/bash


if [[ ! -f .env ]]; then
    cp .env.example .env
    while [ ! -f .env ]; do
        sleep 1
    done
    php artisan key:generate
    php artisan config:cache
fi

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/testing/disks/local storage/framework/views
chmod 777 -R storage bootstrap/cache

php bin/wait-for-composer-install.php
php artisan db:wait
php artisan migrate

apache2-foreground
