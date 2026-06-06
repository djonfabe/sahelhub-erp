#!/usr/bin/env bash
set -euo pipefail

APP_DIR="/var/www/sahelhub"

echo "==> Deploying SahelHub..."
cd "$APP_DIR"

# Allow git to operate on this directory regardless of ownership
git config --global --add safe.directory "$APP_DIR"

# Pull latest code
git pull origin master

# PHP dependencies (no dev, optimised autoloader)
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Frontend assets
npm ci --omit=dev
npm run build

# Run pending migrations
php artisan migrate --force

# Rebuild all caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Ensure storage symlink exists
php artisan storage:link 2>/dev/null || true

# Correct permissions
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Signal queue workers to restart gracefully after current jobs finish
php artisan queue:restart

echo "==> Done. $(date)"
