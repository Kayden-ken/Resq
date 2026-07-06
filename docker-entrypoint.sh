#!/bin/sh

set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Migration and seeding finished."

exec apache2-foreground