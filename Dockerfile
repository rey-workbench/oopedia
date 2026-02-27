# Stage 1: Build Frontend Assets
FROM node:20 AS frontend-builder
WORKDIR /app
RUN npm install -g pnpm
COPY package.json pnpm-lock.yaml ./
RUN pnpm install --frozen-lockfile
COPY . .
RUN pnpm run build

# Stage 2: Serve application (PHP-FPM + Nginx)
FROM php:8.4-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
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

# Configure Nginx for Laravel
RUN echo 'server {\n\
    listen 80;\n\
    index index.php index.html;\n\
    root /var/www/html/public;\n\
    \n\
    location / {\n\
    try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
    \n\
    location ~ \.php$ {\n\
    fastcgi_split_path_info ^(.+\.php)(/.+)$;\n\
    fastcgi_pass 127.0.0.1:9000;\n\
    fastcgi_index index.php;\n\
    include fastcgi_params;\n\
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
    fastcgi_param PATH_INFO $fastcgi_path_info;\n\
    fastcgi_param HTTP_X_FORWARDED_PROTO $http_x_forwarded_proto;\n\
    fastcgi_param HTTPS on;\n\
    }\n\
    }' > /etc/nginx/sites-available/default

# Prepare entrypoint script
RUN echo '#!/bin/bash\n\
    PORT=${PORT:-80}\n\
    sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/sites-available/default\n\
    \n\
    export LOG_CHANNEL=stderr\n\
    \n\
    php artisan config:clear || true\n\
    php artisan storage:link --force || true\n\
    php artisan config:cache || true\n\
    php artisan route:cache || true\n\
    php artisan view:cache || true\n\
    php artisan event:cache || true\n\
    php artisan migrate --force || true\n\
    \n\
    # Start PHP-FPM in background and Nginx in foreground\n\
    php-fpm -D\n\
    echo "Starting Nginx on port $PORT..."\n\
    nginx -g "daemon off;"' > /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["/usr/local/bin/entrypoint.sh"]
