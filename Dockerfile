FROM node:20 AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --legacy-peer-deps
COPY . .
RUN npm run build

FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    build-essential \
    pkg-config \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql intl zip

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy built frontend assets from node builder
COPY --from=node_builder /app/public/build public/build

# Copy application files
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN useradd -m appuser || true
RUN chown -R appuser:appuser /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
