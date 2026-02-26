# Stage 1: Build Frontend Assets
FROM node:20 AS frontend-builder
WORKDIR /app

# Enable pnpm
RUN npm install -g pnpm

# Copy package and lock file
COPY package.json pnpm-lock.yaml ./

# Install dependencies
RUN pnpm install --frozen-lockfile

# Copy the rest of the frontend files
COPY . .

# Build assets (Vite)
RUN pnpm run build


# Stage 2: Serve application (PHP-Apache)
FROM php:8.4-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip opcache \
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

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change Apache document root to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Leverage Docker cache by copying composer files first
COPY composer.json composer.lock ./
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN composer install --no-dev --no-scripts --no-autoloader

# Copy the rest of application files
COPY . .

# Copy built assets from Stage 1
COPY --from=frontend-builder /app/public/build ./public/build

# Finish Composer installation (autoload and scripts)
RUN composer dump-autoload --optimize

# Prepare entrypoint script for running Laravel setup commands
RUN echo '#!/bin/bash\n\
    # Fallback to port 80 if PORT is not set\n\
    PORT=${PORT:-80}\n\
    sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf\n\
    sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/*.conf\n\
    \n\
    # Ensure logging goes to stderr for Back4App dashboard visibility\n\
    export LOG_CHANNEL=stderr\n\
    \n\
    php artisan config:clear\n\
    php artisan storage:link\n\
    php artisan config:cache\n\
    php artisan route:cache\n\
    php artisan view:cache\n\
    php artisan event:cache\n\
    php artisan migrate --force\n\
    \n\
    exec apache2-foreground' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port (Back4App typically uses 80, but entrypoint handles $PORT)
EXPOSE 80

# Start Apache via Entrypoint
CMD ["/usr/local/bin/entrypoint.sh"]
