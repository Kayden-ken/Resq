#!/bin/sh

set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Running database seeders..."
php artisan db:seed --force

echo "Migration and seeding finished."

exec apache2-foreground