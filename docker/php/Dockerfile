FROM php:8.1-fpm-alpine

# Runtime deps stay; build-only deps are removed at the end.
RUN apk add --no-cache git unzip libzip \
    && apk add --no-cache --virtual .build-deps libzip-dev \
    && docker-php-ext-install pdo_mysql zip \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
