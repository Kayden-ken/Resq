#!/bin/sh

set -e

# Copy production env file if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.production .env
fi

# Force set database connection to pgsql
sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=pgsql/g' .env

# Fix permissions
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
mkdir -p /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage/logs

# Clear any cached config
rm -f bootstrap/cache/config.php

# Generate app key if not set
php artisan key:generate --force --no-interaction || true

# Run migrations
php artisan migrate --force --no-interaction || true

exec apache2-foreground