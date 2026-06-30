#!/bin/sh
set -e

echo "=== Cimuning Digital Hub: Container Startup ==="

echo "[1/6] Clearing bootstrap cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "[2/6] Linking storage..."
php artisan storage:link 2>/dev/null || true

echo "[3/6] Running migrations..."
php artisan migrate --force

echo "[4/6] Checking optional seeders..."
case "${RUN_DATABASE_SEEDERS:-false}" in
    true|TRUE|1|yes|YES)
        php artisan db:seed --class=DatabaseSeeder --force
        ;;
    *)
        echo "Seeders skipped."
        ;;
esac

echo "[5/6] Optimizing Laravel..."
php artisan optimize

echo "[6/6] Starting FrankenPHP on port ${PORT:-8080}..."
exec frankenphp run --config /etc/frankenphp/Caddyfile
