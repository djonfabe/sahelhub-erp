#!/usr/bin/env bash
# One-time server provisioning script for Ubuntu 22.04/24.04
# Run as root: bash server-setup.sh
set -euo pipefail

DOMAIN="sahelhub.com"
APP_DIR="/var/www/sahelhub"
DB_NAME="sahelhub"
DB_USER="sahelhub"
DB_PASS="$(openssl rand -base64 24)"   # auto-generated; note it down!

echo "==> Installing packages..."
apt-get update -qq
apt-get install -y -qq \
    nginx mysql-server \
    php8.3 php8.3-fpm php8.3-mysql php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-zip php8.3-gd php8.3-exif php8.3-bcmath \
    php8.3-intl php8.3-fileinfo \
    composer nodejs npm \
    supervisor certbot python3-certbot-nginx \
    git unzip

echo "==> Configuring MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "==> Cloning repository..."
mkdir -p "$APP_DIR"
git clone https://github.com/djonfabe/sahelhub-erp.git "$APP_DIR"
chown -R www-data:www-data "$APP_DIR"

echo "==> Copying configs..."
cp "$APP_DIR/scripts/nginx.conf" "/etc/nginx/sites-available/${DOMAIN}"
ln -sf "/etc/nginx/sites-available/${DOMAIN}" "/etc/nginx/sites-enabled/${DOMAIN}"
rm -f /etc/nginx/sites-enabled/default

cp "$APP_DIR/scripts/supervisor.conf" "/etc/supervisor/conf.d/sahelhub.conf"

echo "==> Setting up application..."
cd "$APP_DIR"
cp scripts/env.production.example .env
# Edit .env now — fill in APP_KEY, DB_PASSWORD, MAIL_*, etc.
# Then run: php artisan key:generate

echo ""
echo "========================================"
echo "DB_PASSWORD generated: ${DB_PASS}"
echo "Add this to .env: DB_PASSWORD=${DB_PASS}"
echo "========================================"
echo ""
echo "Next steps:"
echo "  1. Edit ${APP_DIR}/.env — fill APP_KEY, mail settings, etc."
echo "  2. Run: php artisan key:generate"
echo "  3. Run: php artisan migrate --force"
echo "  4. Run: php artisan storage:link"
echo "  5. Get SSL cert: certbot --nginx -d ${DOMAIN} -d www.${DOMAIN}"
echo "  6. Reload nginx: nginx -t && systemctl reload nginx"
echo "  7. Start supervisor: supervisorctl reread && supervisorctl update"
echo ""
echo "  Then add these secrets to GitHub → Settings → Secrets:"
echo "    DEPLOY_HOST  = your server IP"
echo "    DEPLOY_USER  = root (or deploy user)"
echo "    DEPLOY_SSH_KEY = contents of your private SSH key"
