#!/bin/sh

set -e

echo "Running database migrations..."
php artisan migrate --force

echo "Migration finished."

exec apache2-foreground