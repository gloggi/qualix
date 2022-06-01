FROM php:8.1.0-apache

WORKDIR /var/www

# default-mysql-client is only needed for E2E tests (mysqldump to quickly restore the DB state between tests)
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev libpng-dev curl zlib1g-dev unzip libzip-dev libonig-dev locales default-mysql-client
RUN echo "de_CH.UTF-8 UTF-8" >> /etc/locale.gen && echo "fr_CH.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN yes | pecl install xdebug-3.1.4 && docker-php-ext-enable xdebug
ENV XDEBUG_MODE="debug,develop"
ENV XDEBUG_CONFIG="client_host=docker-host"

RUN docker-php-ext-install pdo pdo_mysql mbstring xml bcmath zip gd

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

ENTRYPOINT bash .docker/entrypoint.sh
