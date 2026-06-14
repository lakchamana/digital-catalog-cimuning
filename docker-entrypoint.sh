#!/bin/sh
set -e

echo "=== Cimuning Digital Hub: Container Startup ==="

# Bersihkan config cache yang mungkin di-bake dengan nilai kosong saat build
echo "[1/5] Clearing config cache..."
php artisan config:clear
php artisan cache:clear

# Build config cache baru dengan env vars Railway yang sudah tersedia
echo "[2/5] Caching config with production env vars..."
php artisan config:cache
php artisan route:cache

# Buat symlink storage (idempotent)
echo "[3/5] Linking storage..."
php artisan storage:link 2>/dev/null || true

# Jalankan migrasi (idempotent - aman dijalankan berulang)
echo "[4/5] Running migrations..."
php artisan migrate --force

# Jalankan seeder (idempotent - seeder pakai firstOrCreate)
echo "[5/5] Running seeders..."
php artisan db:seed --class=DatabaseSeeder --force

echo "=== Startup complete. Starting PHP server on port ${PORT:-8080} ==="

# Jalankan PHP built-in server
exec php -S "0.0.0.0:${PORT:-8080}" -t public
