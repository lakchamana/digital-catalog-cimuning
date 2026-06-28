FROM dunglas/frankenphp:php8.3-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip ca-certificates curl default-mysql-client \
    libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN install-php-extensions intl zip pdo_mysql bcmath gd opcache

# Restore validation uses a dedicated authenticated multipart endpoint.
RUN printf 'upload_max_filesize=250M\npost_max_size=256M\nmax_execution_time=180\n' > /usr/local/etc/php/conf.d/cimuning-uploads.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js 22 for Vite frontend build
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Install PHP dependencies (cached layer)
COPY composer.json composer.lock artisan ./
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --no-scripts --no-interaction

# Install Node dependencies and build frontend (cached layer)
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build && npm prune --omit=dev

# Laravel storage setup
RUN mkdir -p storage/framework/{sessions,views,cache,testing} storage/logs bootstrap/cache \
    && chmod -R a+rw storage bootstrap/cache

# Optimise autoloader after full source copy
# JANGAN jalankan config:cache/route:cache di sini — env vars Railway belum tersedia saat build
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize

# Copy dan set permission entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8080
ENV PORT=8080

# Entrypoint akan: clear cache -> cache config -> migrate -> seed -> start server
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
