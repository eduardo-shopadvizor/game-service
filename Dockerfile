FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

EXPOSE 9000
