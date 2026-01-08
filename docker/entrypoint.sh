#!/bin/sh

set -e

chown -R www-data:www-data storage bootstrap/cache || echo "Falha"
chmod -R 775 storage bootstrap/cache || echo "Falha"

php artisan key:exists || php artisan key:generate

php artisan migrate --force

exec php-fpm
