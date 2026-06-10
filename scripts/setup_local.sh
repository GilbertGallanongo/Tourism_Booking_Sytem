#!/bin/sh
set -e
project_root=$(cd "$(dirname "$0")/.." && pwd)
cd "$project_root"

echo "== Local setup script: configure SQLite and run migrations =="

if [ ! -f .env ]; then
  if [ -f .env.example ]; then
    cp .env.example .env
    echo "Copied .env.example to .env"
  else
    echo "No .env or .env.example found. Create .env manually." >&2
    exit 1
  fi
fi

# Ensure APP_ENV and APP_DEBUG
php -r "file_put_contents('.env', preg_replace('/^APP_ENV=.*/m', 'APP_ENV=local', file_get_contents('.env')));
" || true
php -r "file_put_contents('.env', preg_replace('/^APP_DEBUG=.*/m', 'APP_DEBUG=true', file_get_contents('.env')));
" || true

# Set DB to sqlite and use file sessions
php -r "file_put_contents('.env', preg_replace('/^DB_CONNECTION=.*/m', 'DB_CONNECTION=sqlite', file_get_contents('.env')));
" || true
php -r "file_put_contents('.env', preg_replace('/^DB_DATABASE=.*/m', 'DB_DATABASE='.getcwd().'/database/database.sqlite', file_get_contents('.env')));
" || true
php -r "file_put_contents('.env', preg_replace('/^SESSION_DRIVER=.*/m', 'SESSION_DRIVER=file', file_get_contents('.env')));
" || true

mkdir -p database
if [ ! -f database/database.sqlite ]; then
  touch database/database.sqlite
  echo "Created database/database.sqlite"
fi

# Install deps if vendor missing
if [ ! -d vendor ]; then
  if command -v composer >/dev/null 2>&1; then
    echo "Installing PHP dependencies via composer..."
    composer install --no-interaction --prefer-dist
  else
    echo "composer not found. Please install dependencies manually with 'composer install'" >&2
  fi
fi

# Clear config cache so runtime fallback runs
php artisan config:clear || true

# Run migrations
php artisan migrate --force || true

cat <<EOF

Setup complete.
Run the app with:

  php artisan serve --host=127.0.0.1 --port=8000

Then verify diagnostics:
  curl -sS http://127.0.0.1:8000/_diagnostics | jq .
  tail -n 200 storage/logs/laravel.log
EOF
