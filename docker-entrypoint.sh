#!/bin/sh

set -e

# Run migrations if possible (ignore errors)
echo "Attempting migrations..."
php artisan migrate --force --no-interaction || echo "Migration skipped or failed"

exec apache2-foreground