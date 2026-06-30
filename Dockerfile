FROM dunglas/frankenphp:php8.3-bookworm AS php-base

RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip ca-certificates curl default-mysql-client \
    libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN install-php-extensions intl zip pdo_mysql pdo_sqlite bcmath gd opcache

# Restore validation uses a dedicated authenticated multipart endpoint.
RUN printf 'upload_max_filesize=250M\npost_max_size=256M\nmax_execution_time=180\nexpose_php=Off\ndisplay_errors=Off\nlog_errors=On\nopcache.enable=1\nopcache.validate_timestamps=0\n' > /usr/local/etc/php/conf.d/cimuning-production.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

FROM php-base AS php-dependencies

COPY composer.json composer.lock ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts --no-interaction --no-progress

FROM node:22-bookworm-slim AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY resources ./resources
COPY vite.config.js ./
COPY --from=php-dependencies /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

FROM php-base

WORKDIR /app

COPY --from=php-dependencies /app/vendor ./vendor
COPY . .
COPY --from=frontend /app/public/build ./public/build

COPY Caddyfile /etc/frankenphp/Caddyfile

# Laravel storage setup
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/framework/testing storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# Optimise autoloader after full source copy
# JANGAN jalankan config:cache/route:cache di sini — env vars Railway belum tersedia saat build
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --no-dev --classmap-authoritative

# Copy dan set permission entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080
ENV PORT=8080

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl --fail --silent --show-error "http://127.0.0.1:${PORT:-8080}/up" > /dev/null || exit 1

# Entrypoint menyiapkan cache, migrasi, optional seeder, lalu FrankenPHP.
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
