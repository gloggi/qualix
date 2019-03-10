#!/bin/bash

composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate
fi

apache2-foreground
