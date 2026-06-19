# Deployment Update — Railway + Cloudinary

> Tanggal: 14 Juni 2026
> Konteks: Persiapan dan deployment project ke Railway untuk testing internal tim.
> URL Production: `https://digital-catalog-cimuning-production.up.railway.app/`

---

## Ringkasan Perubahan

Project Cimuning Digital Hub (Laravel 13, PHP 8.3, Filament 5, Livewire 4, Tailwind v4) berhasil dikonfigurasi untuk deployment di Railway dengan Cloudinary sebagai penyimpanan file. Sebelumnya project hanya berjalan di XAMPP lokal. Perubahan ini **tidak mengubah fitur atau logika bisnis** — hanya menambahkan infrastruktur deployment.

---

## Arsitektur Deployment

```
GitHub (main branch)
    │ auto-deploy on push
    ▼
Railway Web Service (Docker container)
    ├── PHP 8.3 + FrankenPHP base image
    ├── Node.js 22 (Vite build)
    ├── Laravel 13 app
    │
    ├──► Railway MySQL Plugin (Database)
    │    └── Migrate + Seed otomatis saat container start
    │
    └──► Cloudinary (File Storage)
         └── Pengganti disk "public" untuk upload foto UMKM/produk
```

Railway menyediakan reverse proxy HTTPS di depan PHP built-in server (HTTP). Semua request masuk melalui: `Browser ──HTTPS──► Railway Proxy ──HTTP──► PHP Server (port 8080)`.

---

## File Baru yang Ditambahkan

### 1. `Dockerfile`

Konfigurasi Docker untuk build di Railway.

- Base image: `dunglas/frankenphp:php8.3-bookworm`.
- Menginstall system dependencies: `git`, `unzip`, `zip`, `curl`, `libicu-dev`, `libzip-dev`, `libpng-dev`, `libjpeg-dev`, `libfreetype6-dev`.
- PHP extensions: `intl`, `zip`, `pdo_mysql`, `bcmath`, `gd`, `opcache`.
- Composer dependencies diinstall dengan `--no-dev --optimize-autoloader`.
- Node.js 22 diinstall untuk build Vite frontend (`npm ci` lalu `npm run build`).
- Storage directories dibuat dan diberi permission writable.
- **PENTING**: Tidak ada `php artisan config:cache` atau `route:cache` di Dockerfile. Env vars Railway belum tersedia saat build — caching dilakukan di `docker-entrypoint.sh` saat runtime.
- Menggunakan `docker-entrypoint.sh` sebagai ENTRYPOINT.

### 2. `docker-entrypoint.sh`

Script yang dijalankan saat container start (runtime). Urutan eksekusi:

1. `php artisan config:clear` — hapus cache config lama (mungkin di-bake saat build dengan nilai kosong).
2. `php artisan cache:clear` — hapus cache aplikasi.
3. `php artisan config:cache` — buat cache config baru dengan env vars Railway yang sudah tersedia.
4. `php artisan route:cache` — cache route untuk performa.
5. `php artisan storage:link` — buat symlink storage (idempotent).
6. `php artisan migrate --force` — jalankan migrasi (idempotent, aman diulang).
7. `php artisan db:seed --class=DatabaseSeeder --force` — jalankan seeder (idempotent, pakai `firstOrCreate`).
8. `php -S 0.0.0.0:${PORT} server.php` — start PHP server dengan router script.

### 3. `server.php`

Router script untuk PHP built-in server. Dibuat karena PHP built-in server punya quirk: path dengan ekstensi file (`.js`, `.css`) yang tidak ada sebagai file statis langsung di-404 tanpa diteruskan ke `index.php`. Ini menyebabkan Livewire JS (`/livewire-xxx/livewire.js`) dan asset Filament gagal load (404).

Logika router:
- Jika file statis ada di `public/` → serve langsung dengan MIME type yang benar.
- Jika file tidak ada → forward request ke `public/index.php` (Laravel router).
- Mendukung MIME types: CSS, JS, JSON, PNG, JPG, GIF, SVG, WEBP, ICO, WOFF/WOFF2, TTF, EOT, MAP.

### 4. `config/cloudinary.php`

File konfigurasi Cloudinary. Membaca tiga kredensial dari environment variables:
- `CLOUDINARY_CLOUD_NAME`
- `CLOUDINARY_API_KEY`
- `CLOUDINARY_API_SECRET`
- `CLOUDINARY_FOLDER` — subfolder di Cloudinary untuk mengisolasi aset project (default: `cimuning`).

### 5. `app/Support/CloudinaryStorage.php`

Custom filesystem adapter yang mengimplementasikan `Illuminate\Contracts\Filesystem\Filesystem`. Dibuat karena package `cloudinary-labs/cloudinary-laravel` tidak kompatibel dengan Laravel 13 (hanya support sampai Laravel 12). Sebagai gantinya, dipakai SDK resmi `cloudinary/cloudinary_php` (v3.1.3) dengan adapter custom ini.

Method yang diimplementasikan secara fungsional:
- `put($path, $contents)` — upload file ke Cloudinary sebagai base64, return secure URL.
- `putFile($path, $file)` — upload dari path file lokal.
- `putFileAs($path, $file, $name)` — upload dengan nama tetap (dipakai Filament file upload).
- `delete($paths)` — hapus file dari Cloudinary berdasarkan public_id.
- `url($path)` — kembalikan URL publik. Jika `$path` sudah berupa URL lengkap (hasil upload), kembalikan langsung.

Method lain dikembalikan dengan nilai default aman (empty array, false, null) karena tidak dipakai oleh fitur upload project ini.

Semua upload disimpan di folder `cimuning/` di Cloudinary. Public ID dibuat dari nama file tanpa ekstensi. Aplikasi tidak lagi menyimpan data tracking klik/scan atau IP pengunjung.

### 6. `nixpacks.toml`

File konfigurasi Nixpacks untuk Railway (alternatif jika tidak pakai Dockerfile). Saat ini **tidak digunakan** karena Railway memprioritaskan Dockerfile jika keduanya ada. File ini tetap ada sebagai referensi. Nixpacks.toml akan dihapus dari remote oleh Railway auto-fix branch sebelumnya, tapi masih bisa dipakai jika Dockerfile dihapus.

---

## File yang Dimodifikasi

### 1. `app/Providers/AppServiceProvider.php`

Dua penambahan:

**a. Force HTTPS di production:**
```php
use Illuminate\Support\Facades\URL;

if ($this->app->environment('production')) {
    URL::forceScheme('https');
}
```
Railway proxy terminates SSL, jadi PHP built-in server hanya melihat HTTP. Tanpa ini, semua URL yang di-generate Laravel pakai `http://` → browser blokir semua asset sebagai Mixed Content.

**b. Registrasi Cloudinary filesystem disk:**
```php
use Illuminate\Support\Facades\Storage;
use App\Support\CloudinaryStorage;

Storage::extend('cloudinary', function () {
    return new CloudinaryStorage();
});
```
Mendaftarkan driver `cloudinary` agar bisa dipakai saat `FILESYSTEM_DISK=cloudinary`.

### 2. `bootstrap/app.php`

Ditambahkan trusted proxy configuration:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```
Agar Laravel membaca header `X-Forwarded-Proto: https` dari Railway reverse proxy. Bekerja bersama `URL::forceScheme('https')` untuk memastikan semua URL HTTPS.

### 3. `config/filesystems.php`

Ditambahkan disk `cloudinary` setelah disk `s3`:
```php
'cloudinary' => [
    'driver' => 'cloudinary',
],
```
Aktif saat `FILESYSTEM_DISK=cloudinary` di `.env` production.

### 4. `.env.example`

Diperbarui menjadi template production-ready. Perubahan utama:
- `APP_ENV=production`, `APP_DEBUG=false`.
- `SESSION_DRIVER=database` dan `CACHE_STORE=database` (agar tidak bergantung filesystem ephemeral Railway). Catatan: saat testing awal, ini bisa diset ke `file` agar lebih simpel.
- `FILESYSTEM_DISK=cloudinary` dengan variable `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`, `CLOUDINARY_FOLDER`.
- Placeholder untuk `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` dari Railway MySQL plugin.

### 5. `composer.json` dan `composer.lock`

Ditambahkan dependency baru:
```json
"cloudinary/cloudinary_php": "^3.1"
```
Package ini menginstall dua library:
- `cloudinary/cloudinary_php` (v3.1.3) — SDK utama.
- `cloudinary/transformation-builder-sdk` (v2.1.5) — dependency SDK.

---

## Environment Variables Railway

Variabel yang harus diset di Railway dashboard (tab Variables pada service Laravel):

| Variable | Nilai | Keterangan |
|---|---|---|
| `APP_NAME` | `Cimuning Digital Hub` | |
| `APP_ENV` | `production` | Wajib agar `URL::forceScheme` aktif |
| `APP_KEY` | `base64:kBX+65g1kKrjubzvARdQSTGCWn84gxW8W9yLpGBLB4I=` | Di-generate dengan `php artisan key:generate --show` |
| `APP_DEBUG` | `false` (atau `true` saat debug) | |
| `APP_URL` | `https://digital-catalog-cimuning-production.up.railway.app` | URL production Railway |
| `DB_CONNECTION` | `mysql` | |
| `DB_HOST` | *(dari Railway MySQL plugin → MYSQLHOST)* | |
| `DB_PORT` | `3306` | |
| `DB_DATABASE` | *(dari Railway MySQL plugin → MYSQLDATABASE)* | |
| `DB_USERNAME` | *(dari Railway MySQL plugin → MYSQLUSER)* | |
| `DB_PASSWORD` | *(dari Railway MySQL plugin → MYSQLPASSWORD)* | |
| `SESSION_DRIVER` | `file` | Pakai `file` untuk testing, `database` untuk production stabil |
| `CACHE_STORE` | `file` | Sama seperti session |
| `QUEUE_CONNECTION` | `sync` | |
| `FILESYSTEM_DISK` | `cloudinary` | Mengarahkan upload ke Cloudinary |
| `CLOUDINARY_CLOUD_NAME` | `dwylcln9i` | |
| `CLOUDINARY_API_KEY` | `367363587628327` | |
| `CLOUDINARY_API_SECRET` | `QmsUlYbEd0IjX20-tRyp7-0Oglk` | |
| `CLOUDINARY_FOLDER` | `cimuning` | |

---

## Masalah yang Ditemukan dan Diselesaikan

### 1. Package Cloudinary Laravel tidak kompatibel

**Masalah**: `cloudinary-labs/cloudinary-laravel` (v1.x–v3.x) hanya mendukung hingga Laravel 12. Project ini memakai Laravel 13.
**Solusi**: Gunakan SDK resmi `cloudinary/cloudinary_php` (v3.1.3) langsung dan buat adapter custom `App\Support\CloudinaryStorage`.

### 2. Config cache di build time dengan env vars kosong

**Masalah**: `php artisan config:cache` yang dijalankan di Dockerfile build phase meng-cache config dengan nilai kosong (env vars Railway belum tersedia saat build). Akibatnya semua koneksi DB gagal → 500.
**Solusi**: Hapus semua `artisan cache` dari Dockerfile build phase. Pindahkan ke `docker-entrypoint.sh` yang jalan di runtime saat env vars sudah tersedia.

### 3. Mixed Content (HTTP vs HTTPS)

**Masalah**: Railway proxy terminates SSL dan meneruskan request via HTTP ke PHP server. Laravel generate semua URL pakai `http://`. Browser memblokir asset `http://` karena halaman diakses via `https://` → CSS, JS, Livewire tidak load.
**Solusi**: Tambahkan `URL::forceScheme('https')` di `AppServiceProvider::boot()` dan `trustProxies(at: '*')` di `bootstrap/app.php`.

### 4. Livewire dan Filament JS gagal load (404)

**Masalah**: PHP built-in server langsung return 404 untuk path dengan ekstensi file (`.js`, `.css`) yang tidak ada sebagai file statis di `public/`. Livewire serve JS-nya lewat route Laravel (`/livewire-xxx/livewire.js`), bukan file statis.
**Solusi**: Buat `server.php` sebagai router script. Router memeriksa apakah file statis ada — jika tidak, forward ke `public/index.php` (Laravel).

### 5. Railway tidak auto-redeploy

**Masalah**: Setelah push ke GitHub, Railway tidak otomatis trigger build baru.
**Solusi**: Pastikan di Railway Settings → Source, branch yang di-watch adalah `main`. Jika tidak ada auto-deploy, trigger manual redeploy dari tab Deployments.

---

## Catatan Penting untuk AI Berikutnya

1. **Jangan hapus `server.php`** — tanpa file ini, Livewire dan Filament JS tidak bisa load di deployment Railway.
2. **Jangan tambahkan `php artisan config:cache` di Dockerfile** — env vars Railway tidak tersedia saat build. Semua caching harus di `docker-entrypoint.sh`.
3. **Jangan ubah `FILESYSTEM_DISK` kembali ke `local` atau `public` di production** — filesystem Railway ephemeral, file upload akan hilang saat redeploy.
4. **Deployment lokal tetap pakai XAMPP** seperti biasa. Perubahan deployment hanya aktif saat `APP_ENV=production`.
5. **Seeder aman dijalankan berulang** — semua seeder pakai `firstOrCreate` / idempotent.
6. **Error `contentscript.js` di browser console** bukan dari project — itu dari extension browser (MetaMask atau sejenisnya). Abaikan.
7. **Branch `railway/fix-deploy-*`** di remote dibuat otomatis oleh Railway saat mencoba auto-fix deployment. Branch ini bisa dihapus, tidak dipakai lagi.
8. **`nixpacks.toml` ada di local tapi mungkin tidak di remote** — Railway pernah menghapusnya lewat auto-fix branch. Tidak masalah karena Railway memprioritaskan `Dockerfile` jika ada.

---

## Daftar Commit Terkait Deployment

| Hash | Pesan | Isi |
|---|---|---|
| `af08b01` | Phase 13: add Railway + Cloudinary deployment Config | Cloudinary adapter, config, filesystems, env.example, nixpacks.toml |
| `3a0dc4e` | fix: add railpack.json with required PHP extensions | *(dibuat Railway auto-fix)* |
| `6256010` | fix: add Dockerfile with ext-intl and ext-zip | Dockerfile awal *(dibuat Railway auto-fix)* |
| `717382a` | fix: use entrypoint script so config:cache runs at runtime | docker-entrypoint.sh, Dockerfile update ke ENTRYPOINT |
| `6d59682` | fix: force HTTPS and trust proxy to fix Mixed Content | AppServiceProvider + bootstrap/app.php |
| `950ade2` | fix: add router script to fix Livewire/Filament JS 404 | server.php + docker-entrypoint.sh update |
