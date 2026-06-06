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

# Frontend assets (tsc type-checking already done in CI; vite build only)
npm ci
npm run build:deploy

# ── Postfix (relai mail local) ──────────────────────────────────────────────
if ! dpkg -l postfix 2>/dev/null | grep -q '^ii'; then
    echo "==> Installation de Postfix..."
    DEBIAN_FRONTEND=noninteractive apt-get install -y postfix
fi
postconf -e "myhostname = sahelhub.com"
postconf -e "mydomain = sahelhub.com"
postconf -e "myorigin = sahelhub.com"
postconf -e "inet_interfaces = loopback-only"
postconf -e "inet_protocols = ipv4"
postconf -e "mydestination = sahelhub.com, localhost.sahelhub.com, localhost"
postconf -e "relayhost ="
postconf -e "smtpd_banner = \$myhostname ESMTP"
postconf -e "smtpd_tls_security_level = none"
systemctl enable postfix --quiet 2>/dev/null || true
systemctl restart postfix
# ────────────────────────────────────────────────────────────────────────────

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
