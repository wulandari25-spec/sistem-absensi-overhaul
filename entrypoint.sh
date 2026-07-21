#!/bin/sh

# Ensure SQLite fallback file exists
mkdir -p database storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database

# Create storage symlink
php artisan storage:link || true

# Run migrations & seeders gracefully (won't crash container if DB is initializing)
php artisan migrate --force || true
php artisan db:seed --force || true

# Clear cache
php artisan config:clear || true
php artisan cache:clear || true

# Start web server on Railway assigned PORT or fallback 8080
TARGET_PORT="${PORT:-8080}"
echo "Server starting on 0.0.0.0:${TARGET_PORT}..."
exec php -S 0.0.0.0:${TARGET_PORT} -t public
