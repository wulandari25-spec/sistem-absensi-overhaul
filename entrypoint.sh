#!/bin/sh

export APP_DEBUG=true
export SESSION_DRIVER=file
export CACHE_STORE=file
export LOG_CHANNEL=stderr

# Ensure SQLite fallback file exists
mkdir -p database storage/framework/views storage/framework/cache/data storage/framework/sessions storage/logs
touch database/database.sqlite
chmod -R 777 storage bootstrap/cache database

# Ensure APP_KEY exists
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force || true
fi

# Create storage symlink
php artisan storage:link || true

# Run fresh migrations & seeders to guarantee all tables are created
php artisan migrate:fresh --seed --force || php artisan migrate --force || true

# Clear cache
php artisan config:clear || true
php artisan cache:clear || true

# Start web server on Railway assigned PORT or fallback 8080
TARGET_PORT="${PORT:-8080}"
echo "Server starting on 0.0.0.0:${TARGET_PORT}..."
exec php -S 0.0.0.0:${TARGET_PORT} -t public
