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
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    default-mysql-client \
    nginx \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        opcache \
        pcntl \
        sockets \
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

# Configure Nginx
RUN echo 'server { \
    listen ${PORT:-8080}; \
    server_name _; \
    root /var/www/html/public; \
    add_header X-Frame-Options "SAMEORIGIN"; \
    add_header X-Content-Type-Options "nosniff"; \
    index index.php; \
    charset utf-8; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location = /favicon.ico { access_log off; log_not_found off; } \
    location = /robots.txt { access_log off; log_not_found off; } \
    error_page 404 /index.php; \
    location ~ \.php$ { \
        try_files $uri =404; \
        fastcgi_split_path_info ^(.+\.php)(/.+)$; \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_index index.php; \
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
    location ~ /\.(?!well-known).* { \
        deny all; \
    } \
}' > /etc/nginx/sites-available/default

# Configure Supervisor
RUN echo '[supervisord] \
nodaemon=true \
user=root \
logfile=/var/log/supervisord.log \
loglevel=info \
\
[program:php-fpm] \
command=/usr/local/sbin/php-fpm \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
autorestart=true \
\
[program:nginx] \
command=/usr/sbin/nginx -g "daemon off;" \
stdout_logfile=/dev/stdout \
stdout_logfile_maxbytes=0 \
stderr_logfile=/dev/stderr \
stderr_logfile_maxbytes=0 \
autorestart=true \
' > /etc/supervisor/supervisord.conf

# Prepare entrypoint script
RUN cat <<'EOF' > /usr/local/bin/entrypoint.sh
#!/bin/bash

# Check if DB_HOST is already set (from compose/env), otherwise default to 'db'
export DB_HOST=${DB_HOST:-db}
export LOG_CHANNEL=stderr

# Wait for database to be ready
echo "Waiting for database ($DB_HOST)..."
# Simple check if DB is reachable
for i in {1..30}; do
    if mysqladmin ping -h "$DB_HOST" --silent; then
        break
    fi
    echo "Database at $DB_HOST is unavailable - sleeping"
    sleep 2
done

# Ensure PORT is used in Nginx config
sed -i "s/listen 8080;/listen ${PORT:-8080};/g" /etc/nginx/sites-available/default

php artisan config:clear || true
php artisan storage:link --force || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true
php artisan event:cache || true

# Only run migrations if DB is actually configured correctly
if [ ! -z "$DB_HOST" ]; then
    echo "Starting migrations..."
    php artisan migrate --force || true
fi

echo "Starting application on port ${PORT:-8080}..."
exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
EOF

RUN chmod +x /usr/local/bin/entrypoint.sh

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080
CMD ["/usr/local/bin/entrypoint.sh"]