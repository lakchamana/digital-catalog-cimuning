FROM dunglas/frankenphp:php8.3-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip ca-certificates curl \
    libicu-dev libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && rm -rf /var/lib/apt/lists/*

# Install required PHP extensions
RUN install-php-extensions intl zip pdo_mysql bcmath gd opcache

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
    && chmod -R a+rw storage

# Optimise autoloader after full source copy
RUN COMPOSER_ALLOW_SUPERUSER=1 composer dump-autoload --optimize

EXPOSE 8080
ENV PORT=8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
