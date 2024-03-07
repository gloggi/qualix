FROM php:8.2-apache

WORKDIR /var/www

# default-mysql-client is only needed for E2E tests (mysqldump to quickly restore the DB state between tests)
RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev libpng-dev curl zlib1g-dev unzip libzip-dev libonig-dev locales default-mysql-client
RUN echo "de_CH.UTF-8 UTF-8" >> /etc/locale.gen && echo "fr_CH.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions xdebug @composer
RUN docker-php-ext-install pdo pdo_mysql mbstring xml bcmath zip gd
COPY docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

ENTRYPOINT bash .docker/entrypoint.sh
