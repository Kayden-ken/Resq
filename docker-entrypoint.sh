#!/bin/sh

set -e

echo "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "Running database migrations..."
php artisan migrate --force

echo "Starting Apache..."
exec apache2-foreground