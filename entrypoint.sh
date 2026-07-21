#!/bin/sh

export APP_DEBUG=true
export PHP_CLI_SERVER_WORKERS=4
export SESSION_DRIVER=database
export CACHE_STORE=file
export LOG_CHANNEL=stderr
export APP_URL=https://sistem-absensi-overhaul-production.up.railway.app

# Database auto-configuration (MySQL if Railway MySQL plugin present, otherwise SQLite)
if [ -n "$MYSQLHOST" ]; then
    export DB_CONNECTION=mysql
    export DB_HOST="$MYSQLHOST"
    export DB_PORT="${MYSQLPORT:-3306}"
    export DB_DATABASE="${MYSQLDATABASE:-railway}"
    export DB_USERNAME="${MYSQLUSER:-root}"
    export DB_PASSWORD="$MYSQLPASSWORD"
elif [ "$DB_HOST" = "127.0.0.1" ] || [ -z "$DB_HOST" ]; then
    export DB_CONNECTION=sqlite
    export DB_DATABASE=/app/database/database.sqlite
fi

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

# Run migrations & seeders safely without wiping existing attendance data on restart
php artisan migrate --force || true
php artisan db:seed --force || true

# Clear cache
php artisan config:clear || true
php artisan cache:clear || true

# Start web server on Railway assigned PORT or fallback 8080
TARGET_PORT="${PORT:-8080}"
echo "Server starting on 0.0.0.0:${TARGET_PORT}..."
exec php -S 0.0.0.0:${TARGET_PORT} -t public
