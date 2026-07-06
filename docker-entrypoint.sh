#!/bin/sh

set -e

# Fix permissions
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage

# Run migrations if possible
php artisan migrate --force --no-interaction || true

exec apache2-foreground