FROM php:8.3-cli-alpine

RUN apk add --no-cache postgresql-dev unzip git \
    && docker-php-ext-install pdo pdo_pgsql

COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
