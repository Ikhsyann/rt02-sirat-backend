#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"

exec 9>/tmp/sirat-backend-deploy.lock
flock -n 9 || { echo "deploy backend sedang berjalan"; exit 1; }

git pull --ff-only
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci --no-audit --no-fund
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
