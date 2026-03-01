# Stage 1: Build Frontend Assets
FROM node:20 AS frontend-builder
WORKDIR /app
RUN npm install -g pnpm
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . .
RUN pnpm run build

# Stage 2: Get RoadRunner Binary
FROM ghcr.io/roadrunner-server/roadrunner:2024.1.0 AS roadrunner

# Stage 3: Serve application (PHP CLI + Octane)
FROM php:8.4-cli

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip opcache pcntl sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure OPcache for production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Leverage Docker cache
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy application files
COPY . .
COPY --from=frontend-builder /app/public/build ./public/build

# Finish Composer installation
RUN composer dump-autoload --optimize

# Copy RoadRunner binary
COPY --from=roadrunner /usr/bin/rr /usr/bin/rr

# Prepare entrypoint script
RUN cat <<'EOF' > /usr/local/bin/entrypoint.sh
#!/bin/bash
PORT=${PORT:-8080}

export LOG_CHANNEL=stderr

php artisan config:clear || true
php artisan storage:link --force || true
php artisan octane:install --server=roadrunner --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true
php artisan migrate --force || true

echo "Starting Laravel Octane on port ${PORT}..."
exec php artisan octane:start --server=roadrunner --host=0.0.0.0 --port=${PORT}
EOF

RUN chmod +x /usr/local/bin/entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod +x /usr/bin/rr

EXPOSE 8080
CMD ["/usr/local/bin/entrypoint.sh"]
