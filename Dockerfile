# Multi-stage build for Laravel + Vite application

# Stage 1: Build frontend assets with Node
FROM node:24-alpine AS node_builder
WORKDIR /app
COPY package*.json ./
COPY vite.config.js ./
COPY resources/ resources/
RUN npm ci --legacy-peer-deps && npm run build && npm cache clean --force

# Stage 2: Build PHP dependencies
FROM composer:2 AS composer_builder
WORKDIR /app
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV COMPOSER_MEMORY_LIMIT=-1
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Stage 3: Final runtime image
FROM php:8.2-fpm-alpine

# Install system packages and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip \
    && apk del libzip-dev libpng-dev oniguruma-dev libxml2-dev

# Configure PHP-FPM to listen on TCP 127.0.0.1:9000 for nginx
RUN mkdir -p /usr/local/etc/php-fpm.d && \
    echo '[www]' > /usr/local/etc/php-fpm.d/www.conf && \
    echo 'user = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'group = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen = 127.0.0.1:9000' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen.owner = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'listen.group = www-data' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_children = 5' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.d/www.conf && \
    echo 'pm.max_spare_servers = 3' >> /usr/local/etc/php-fpm.d/www.conf

# Create runtime directories
RUN mkdir -p \
    /var/log/supervisor \
    /var/run/supervisor \
    /var/run/php \
    /var/cache/nginx \
    /var/log/nginx \
    /var/www/html

# Configure Nginx using RUN with echo (heredoc not supported on all Docker versions)
RUN mkdir -p /etc/nginx/http.d && \
    echo 'server {' > /etc/nginx/http.d/default.conf && \
    echo '    listen 0.0.0.0:80;' >> /etc/nginx/http.d/default.conf && \
    echo '    listen [::]:80;' >> /etc/nginx/http.d/default.conf && \
    echo '    root /var/www/html/public;' >> /etc/nginx/http.d/default.conf && \
    echo '    index index.php;' >> /etc/nginx/http.d/default.conf && \
    echo '    client_max_body_size 10G;' >> /etc/nginx/http.d/default.conf && \
    echo '    location / {' >> /etc/nginx/http.d/default.conf && \
    echo '        try_files $uri $uri/ /index.php?$query_string;' >> /etc/nginx/http.d/default.conf && \
    echo '    }' >> /etc/nginx/http.d/default.conf && \
    echo '    location ~ \.php$ {' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_pass 127.0.0.1:9000;' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_index index.php;' >> /etc/nginx/http.d/default.conf && \
    echo '        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;' >> /etc/nginx/http.d/default.conf && \
    echo '        include fastcgi_params;' >> /etc/nginx/http.d/default.conf && \
    echo '    }' >> /etc/nginx/http.d/default.conf && \
    echo '}' >> /etc/nginx/http.d/default.conf

# Setup Supervisor directories and copy config files
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor /var/run/supervisor

COPY supervisord.conf /etc/supervisord.conf
COPY php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf
COPY nginx.conf /etc/supervisor/conf.d/nginx.conf
COPY laravel-tail.conf /etc/supervisor/conf.d/laravel-tail.conf

WORKDIR /var/www/html

# Copy application
COPY . .

# Copy dependencies from builder stages
COPY --from=composer_builder /app/vendor ./vendor
COPY --from=node_builder /app/public ./public

# Setup Laravel directories
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 755 storage bootstrap/cache

# Copy and prepare entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]