#!/bin/sh
set -e

cd /var/www/html

if [ -z "${APP_KEY:-}" ]; then
  echo "WARNING: APP_KEY is not set. Set APP_KEY in Railway variables for stable sessions." >&2
fi

if [ -n "${RAILWAY_VOLUME_MOUNT_PATH:-}" ] && [ -z "${PUBLIC_STORAGE_PATH:-}" ]; then
  export PUBLIC_STORAGE_PATH="${RAILWAY_VOLUME_MOUNT_PATH%/}/storage/app/public"
fi

if [ -n "${PUBLIC_STORAGE_PATH:-}" ]; then
  mkdir -p "$PUBLIC_STORAGE_PATH"
else
  mkdir -p storage/app/public
fi

php artisan storage:link || true
php artisan migrate --force

php scripts/migrate_sqlite_to_pg.php --if-empty

if [ -f public/build/assets/app.css ]; then
  mkdir -p public/css
  cp public/build/assets/app.css public/css/app.css
fi

if [ -f public/build/assets/app.js ]; then
  mkdir -p public/js
  cp public/build/assets/app.js public/js/app.js
fi

php artisan config:cache
php artisan view:cache

php -S 0.0.0.0:${PORT:-8080} -t public
