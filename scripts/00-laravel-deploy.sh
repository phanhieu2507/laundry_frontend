#!/usr/bin/env bash

echo "Updating Composer to version 2..."
composer self-update --2

echo "Running composer..."
composer install --no-dev --verbose --working-dir=/var/www/html

# Check if the vendor autoload file exists
if [ ! -f /var/www/html/vendor/autoload.php ]; then
    echo "Composer install failed or autoload file not found."
    exit 1
fi

echo "Generating application key..."
php /var/www/html/artisan key:generate --show

echo "Caching config..."
php /var/www/html/artisan config:cache

echo "Caching routes..."
php /var/www/html/artisan route:cache

echo "Running migrations..."
php /var/www/html/artisan migrate --force

echo "Running seed..."
php /var/www/html/artisan db:seed
