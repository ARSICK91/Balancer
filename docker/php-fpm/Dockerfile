FROM php:8.1.0-fpm-alpine

# Install packages
RUN apk add --no-cache curl git build-base zlib-dev oniguruma-dev autoconf bash
RUN docker-php-ext-install pdo pdo_mysql
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/memory-limit.ini
# Source code
COPY ./ /var/www
WORKDIR /var/www

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей
RUN composer install --no-interaction

CMD ["php-fpm"]

EXPOSE 9000
