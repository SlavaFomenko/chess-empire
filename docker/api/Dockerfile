ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm

WORKDIR /var/www/chess-empire/api

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
        git \
        libyaml-dev \
        libssh-dev \
        libcurl4-openssl-dev \
        libxslt-dev \
        libxml2-dev \
        libpng-dev \
        libjpeg-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql gd pdo sockets bcmath xsl xml soap pcntl \
    && docker-php-ext-enable gd

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN pecl install yaml \
    && echo "extension=yaml.so" > /usr/local/etc/php/conf.d/ext-yaml.ini \
    && docker-php-ext-enable yaml
