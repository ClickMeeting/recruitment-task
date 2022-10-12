FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y zlib1g-dev libicu-dev libzip-dev zip unzip \
    && docker-php-ext-install intl opcache pdo pdo_mysql \
    && pecl install apcu \
    && docker-php-ext-enable apcu \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
