# Cimuning Digital Hub

Cimuning Digital Hub adalah direktori dan katalog digital UMKM Cimuning, Kota Bekasi. Pengunjung dapat mencari produk/jasa, membuka profil UMKM, melihat lokasi, dan menghubungi owner secara langsung. Platform tidak menyediakan cart, checkout, payment, ongkir, atau transaksi internal.

## Stack

- Laravel 13, PHP 8.3, MySQL/MariaDB.
- Blade, Tailwind CSS 4, Alpine.js, dan Livewire 4.
- Filament 5 untuk panel admin dan owner di `/admin`.
- Cloudinary untuk media production.
- FrankenPHP/Caddy untuk deployment Docker.

## Setup Lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
npm install
npm run build
php artisan serve
```

Sesuaikan koneksi MySQL, Cloudinary, dan URL aplikasi di `.env`. Data seeder hanya untuk development/testing.

## Pemeriksaan

```bash
php artisan test
vendor/bin/pint --test
composer audit --locked
npm audit --audit-level=high
npm run build
```

Untuk environment hosting yang sudah dikonfigurasi:

```bash
php artisan app:production-check --with-external --require-scheduler
php artisan media:diagnose --upload
```

Kedua command tidak menampilkan nilai secret. Jangan menjalankan `key:generate` pada database production yang sudah aktif.

## Role dan Batas Akses

- Public user mencari UMKM/produk tanpa login.
- Owner mengelola profil dan produk miliknya sendiri.
- Admin mengelola kategori, akun owner, verifikasi, moderasi, audit, dan backup tanpa melihat password owner atau menulis ulang konten owner.

## Deployment

- Template environment: `.env.production.example`.
- Panduan cPanel dan Docker/VPS: `docs/PRODUCTION_DEPLOYMENT.md`.
- Pemulihan backup: `docs/BACKUP_RESTORE_RUNBOOK.md`.
- Document root web wajib mengarah ke folder `public/`.
- Scheduler wajib menjalankan `php artisan schedule:run` setiap menit.
- `RUN_DATABASE_SEEDERS` harus `false` untuk hosting publik.
- SMTP dan reset password email tetap nonaktif sampai layanan email production tersedia.

## Keamanan

Jangan commit `.env`, credential, backup database, atau file hasil upload. Sebelum publikasi, gunakan APP_KEY dan credential baru, aktifkan HTTPS, jalankan production check, uji restore backup, dan review Kebijakan Privasi serta Syarat Penggunaan.
