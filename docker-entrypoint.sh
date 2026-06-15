#!/bin/bash
set -e

# Check if vendor directory exists or is empty
if [ ! -d "vendor" ] || [ -z "$(ls -A vendor)" ]; then
    echo "Vendor directory not found or empty. Installing dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

# Wait for database to be ready
echo "Waiting for database connection..."
# Read database config from .env.docker (always available in Docker), fallback .env
DB_HOST="$(grep -oP '^database\.default\.hostname\s*=\s*\K.*' .env.docker 2>/dev/null | head -1 | xargs || echo 'db')"
DB_USER="$(grep -oP '^database\.default\.username\s*=\s*\K.*' .env.docker 2>/dev/null | head -1 | xargs || echo 'root')"
DB_PASS="$(grep -oP '^database\.default\.password\s*=\s*\K.*' .env.docker 2>/dev/null | head -1 | xargs || echo '')"
DB_PORT="$(grep -oP '^database\.default\.port\s*=\s*\K.*' .env.docker 2>/dev/null | head -1 | xargs || echo '3306')"

echo "DB Host: $DB_HOST, DB User: $DB_USER, DB Port: $DB_PORT"

for i in $(seq 1 30); do
    if mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -P"$DB_PORT" --skip-ssl -e "SELECT 1" 2>/dev/null; then
        echo "Database is ready!"
        break
    fi
    echo "Waiting for database... attempt $i/30"
    sleep 2
done

# Run database migrations
echo "Running database migrations..."
php spark migrate --all

# Run database seeds
# echo "Running database seeds..."
# php spark db:seed DatabaseSeeder

# Fix permissions for writable and public/uploads directory to ensure www-data can write
if [ -d "writable" ]; then
    chown -R www-data:www-data writable
    # Also ensure host user can still write for local development
    chmod -R o+rwX writable
fi

if [ -d "public/uploads" ]; then
    chown -R www-data:www-data public/uploads
    # Also ensure host user can still write for local dev/testing
    chmod -R o+rwX public/uploads/tmp
fi

# Ensure public directory is traversable by www-data
chmod o+x /var/www/html /var/www/html/public

# Ensure all app & public & vendor files are readable by www-data (from volume mount)
chmod -R o+rX /var/www/html/app /var/www/html/public /var/www/html/vendor /var/www/html/.env 2>/dev/null || true

# Execute the original entrypoint
exec docker-php-entrypoint "$@"
