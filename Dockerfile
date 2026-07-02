FROM node:20 AS node_builder
WORKDIR /app
COPY package*.json ./
RUN npm ci --legacy-peer-deps
COPY . .
RUN rm -rf public/build && npm run build && ls -la public/build/assets && node -e "const fs=require('fs');const crypto=require('crypto');const paths=['public/build/assets/app.css','public/build/assets/app.js'];paths.forEach(p=>{const d=fs.readFileSync(p);console.log(p, d.length, crypto.createHash('sha256').update(d).digest('hex'));})"

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
    libsqlite3-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_pgsql pdo_sqlite intl zip

RUN { \
    echo "upload_max_filesize=50M"; \
    echo "post_max_size=60M"; \
    echo "memory_limit=512M"; \
    echo "max_file_uploads=50"; \
    echo "max_execution_time=300"; \
    echo "max_input_time=300"; \
    echo "upload_tmp_dir=/tmp"; \
  } > /usr/local/etc/php/conf.d/uploads.ini

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy built frontend assets from node builder
COPY --from=node_builder /app/public/build public/build

# Copy application files
COPY . .

RUN php -r "foreach (array('public/build/assets/app.css','public/build/assets/app.js') as \$path) { if (!file_exists(\$path)) { fwrite(STDERR, \"MISSING ASSET: \$path\\n\"); exit(1); } echo \"\$path size=\" . filesize(\$path) . \" sha256=\" . hash_file('sha256', \$path) . \"\\n\"; }"

RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

RUN useradd -m appuser || true
RUN chown -R appuser:appuser /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod +x /var/www/html/scripts/railway-start.sh

EXPOSE 8080

CMD ["sh", "/var/www/html/scripts/railway-start.sh"]
