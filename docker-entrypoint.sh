#!/bin/bash

# Add hostname of docker host for XDebug to connect to, on Linux. On Win and Mac, Docker automatically resolves host.docker.internal
if [[ ! `getent hosts host.docker.internal | cut -d' ' -f1` ]]; then
    if ! grep "host.docker.internal" /etc/hosts > /dev/null ; then
        DOCKER_INTERNAL_IP=`/sbin/ip -4 route list match 0/0 | awk '{ print $3 }' | head -n 1`
        echo -e "$DOCKER_INTERNAL_IP\thost.docker.internal" >> /etc/hosts
        echo "Added host.docker.internal to /etc/hosts"
    fi
fi

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
