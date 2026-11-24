FROM php:8.3-fpm-bookworm AS php-base

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev libpng-dev \
    libjpeg62-turbo-dev libfreetype6-dev libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo_mysql mbstring bcmath exif pcntl gd intl zip opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

FROM php-base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

FROM node:24-alpine AS frontend

WORKDIR /app

COPY package.json ./

RUN npm install --no-audit --no-fund

COPY resources ./resources
COPY public ./public
COPY vite.config.js ./

RUN npm run build

FROM php-base AS app

WORKDIR /var/www/html

COPY . .
COPY --from=vendor /var/www/html/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
