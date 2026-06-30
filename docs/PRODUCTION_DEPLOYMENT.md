# Production Deployment

Dokumen ini menjadi checklist saat memilih dan memasang Cimuning Digital Hub pada shared hosting cPanel atau VPS/Docker. Railway tetap dianggap environment testing tim.

## Syarat Wajib Hosting

- PHP 8.3 dengan `ctype`, `dom`, `fileinfo`, `filter`, `intl`, `json`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, `xmlreader`, dan `zip`.
- MySQL 8 atau MariaDB yang kompatibel, HTTPS, SSH, Composer 2, cron, serta document root yang dapat diarahkan ke `public/`.
- `mysqldump`, `proc_open`, dan ZIP AES-256 harus tersedia agar fitur Backup Data admin berfungsi.
- Storage dan `bootstrap/cache` harus writable oleh user PHP, tanpa membuka permission global yang berlebihan.
- Provider yang tidak memenuhi document root, cron, atau backup prerequisites tidak direkomendasikan.

## Environment Production

1. Salin `.env.production.example` menjadi `.env` hanya di server.
2. Buat APP_KEY baru sekali sebelum database production digunakan.
3. Isi domain final pada `APP_URL` dan `TRUSTED_HOSTS`.
4. cPanel langsung: kosongkan `TRUSTED_PROXIES`, gunakan `LOG_STACK=daily`.
5. Reverse proxy Docker: isi proxy tepercaya; `*` hanya bila aplikasi tidak dapat diakses dengan melewati proxy. Gunakan `LOG_STACK=stderr`.
6. Pertahankan `APP_DEBUG=false`, `AUTH_PASSWORD_RESET_ENABLED=false`, dan `RUN_DATABASE_SEEDERS=false`.
7. Isi credential MySQL dan Cloudinary langsung di secret manager/panel hosting.

## Deployment cPanel

1. Buat backup database aktif dan recovery kit secret.
2. Upload atau checkout release ke direktori di luar `public_html`.
3. Arahkan document root domain ke `<project>/public`. Jangan memindahkan `.env`, `vendor`, atau source code ke public root.
4. Jalankan:

```bash
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan storage:link
php artisan migrate --force
php artisan optimize
```

Jika Node.js tidak tersedia di hosting, build `public/build` melalui CI dan deploy artifact release yang sama.

5. Tambahkan cron setiap menit:

```cron
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

6. Setelah cron berjalan minimal satu menit, jalankan:

```bash
php artisan app:production-check --with-external --require-scheduler
php artisan media:diagnose --upload
```

## Deployment VPS/Docker

1. Letakkan database dan reverse proxy/TLS pada jaringan privat yang sesuai.
2. Build image dari commit yang sudah lulus CI.
3. Pasang seluruh variable production melalui secret manager, bukan image Docker.
4. Entrypoint menjalankan migration, melewati seeder, mengoptimalkan Laravel, lalu memulai FrankenPHP.
5. Jalankan scheduler sebagai container/cron terpisah dengan image dan environment yang sama:

```bash
php artisan schedule:work
```

6. Route `/up` digunakan healthcheck container. Uptime monitor eksternal boleh memeriksa route ini tanpa autentikasi.

## Release dan Rollback

1. Pastikan CI hijau dan buat backup sebelum release.
2. Aktifkan maintenance mode bila perubahan berisiko atau membutuhkan perpindahan database.
3. Deploy kode, jalankan migration, optimize, production check, media diagnostic, dan smoke test public/admin.
4. Jika gagal sebelum migration, kembalikan release sebelumnya.
5. Jika migration sudah berjalan, gunakan rollback yang memang telah diuji atau pulihkan database staging-first sesuai runbook; jangan melakukan rollback buta.
6. Setelah stabil, nonaktifkan maintenance mode dan catat waktu serta operator release.

## Monitoring dan Log

- cPanel menyimpan log harian di `storage/logs` selama 14 hari. Batasi akses folder tersebut hanya untuk user aplikasi dan tinjau error minimal setiap hari pada minggu pertama peluncuran.
- Docker/VPS mengirim log ke `stderr`; platform container wajib menyimpan dan merotasi output tersebut. Jangan menyalakan `APP_DEBUG` untuk membaca error production.
- Pasang uptime monitor eksternal pada `/up`. Respons non-200 berarti aplikasi, database, atau cache perlu diperiksa.
- Periksa heartbeat scheduler dengan `php artisan app:production-check --require-scheduler`; pemeriksaan ini tidak menggantikan review log dan latihan restore.
- Log tidak boleh dipakai untuk menyimpan password, token, passphrase backup, credential database, atau secret Cloudinary.

## Checklist Sebelum Publikasi

- Domain utama dan `www` mengarah konsisten ke HTTPS.
- APP_KEY, database, Cloudinary, dan credential panel telah dirotasi untuk production.
- `/`, `/produk`, `/umkm`, `/kategori`, `/admin`, `/up`, sitemap, dan media berhasil dibuka.
- Registrasi owner, verifikasi admin, moderasi, upload, backup, dan restore drill telah diuji.
- Cron heartbeat terdeteksi dan log production dapat dibaca operator.
- Backup terenkripsi disimpan di luar server beserta passphrase terpisah.
- Kebijakan Privasi dan Syarat Penggunaan telah direview pengelola/hukum.
- SMTP tetap nonaktif sampai kanal email production siap.
- Data demo dibersihkan pada langkah terakhir sebelum database dibuka untuk masyarakat.
