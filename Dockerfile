FROM php:7.2.15-apache

WORKDIR /var/www

RUN apt-get -y update && apt-get -y upgrade && apt-get -y install libxml2-dev curl zlib1g-dev unzip locales
RUN echo "de_CH.UTF-8 UTF-8" >> /etc/locale.gen && locale-gen

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN yes | pecl install xdebug-2.7.0 && docker-php-ext-enable xdebug
ENV XDEBUG_CONFIG="remote_connect_back=1 remote_enable=1"

RUN docker-php-ext-install pdo pdo_mysql mbstring xml bcmath zip

RUN a2enmod rewrite
COPY apache-vhost.conf /etc/apache2/sites-enabled/000-default.conf

ENTRYPOINT bash docker-entrypoint.sh
