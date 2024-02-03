FROM php:8.1.10-fpm

RUN pecl install xdebug-3.1.6 && docker-php-ext-enable xdebug

RUN docker-php-ext-install opcache

# GD is needed so Kirby can generate thumbnails.
# https://stackoverflow.com/a/62449355/3130281
RUN apt-get update && apt-get install -y zlib1g-dev libpng-dev libjpeg-dev
RUN docker-php-ext-configure gd --with-jpeg && docker-php-ext-install gd

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Fixes this: Failed to download getkirby/composer-installer from dist: The zip
# extension and unzip/7z commands are both missing, skipping.
RUN apt-get install zip unzip

COPY composer.json .
COPY composer.lock .
RUN composer update
RUN composer install