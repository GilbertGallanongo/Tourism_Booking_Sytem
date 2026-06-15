#!/bin/bash

# Build script for Railway deployment

echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

echo "Installing Node dependencies..."
npm install --production

echo "Building frontend assets..."
npm run build

echo "Running migrations..."
php artisan migrate --force

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Build complete!"

# Ensure Blade legacy asset paths are populated (copy Vite output to public/css and public/js)
if [ -f public/build/assets/app.css ]; then
	mkdir -p public/css
	cp public/build/assets/app.css public/css/app.css
	echo "Copied public/build/assets/app.css -> public/css/app.css"
fi

if [ -f public/build/assets/app.js ]; then
	mkdir -p public/js
	cp public/build/assets/app.js public/js/app.js
	echo "Copied public/build/assets/app.js -> public/js/app.js"
fi
