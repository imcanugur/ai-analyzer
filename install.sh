#!/bin/bash
set -e

composer install --no-interaction --prefer-dist --optimize-autoloader

if ! grep -q "APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

if [ "$RUN_MIGRATIONS" = "true" ]; then
  php artisan migrate --force
fi

if [ "$RUN_SEEDERS" = "true" ]; then
  php artisan db:seed --force
fi

php artisan filament:assets

php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan optimize

exec "$@"
