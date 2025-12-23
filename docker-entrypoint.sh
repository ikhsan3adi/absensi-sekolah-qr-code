#!/bin/bash
set -e

# Check if vendor directory exists or is empty
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
    echo "Vendor directory not found or empty. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Run database migrations
echo "Running database migrations..."
php spark migrate --all

# Fix permissions for writable and public/uploads directory to ensure www-data can write
if [ -d "writable" ]; then
    chown -R www-data:www-data writable
fi

if [ -d "public/uploads" ]; then
    chown -R www-data:www-data public/uploads
fi

# Execute the original entrypoint
exec docker-php-entrypoint "$@"
