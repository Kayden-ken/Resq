#!/bin/sh

set -e

# Copy production env file if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.production .env
fi

# Fix permissions
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Generate app key if not set
php artisan key:generate --force --no-interaction || true

# Run migrations
php artisan migrate --force --no-interaction || true

exec apache2-foreground