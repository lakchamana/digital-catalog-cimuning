# Backup & Restore Runbook

## Tujuan dan Batas

Runbook ini melindungi database dan media Cimuning Digital Hub tanpa menyediakan restore langsung dari dashboard production.

- Target kehilangan data maksimum (RPO): 72 jam.
- Target pemulihan operasional (RTO): sekitar 4 jam setelah akses infrastruktur tersedia.
- Backup dashboard hanya berisi database.
- Media dilindungi melalui fitur backup Cloudinary.
- Restore aktual selalu diuji pada database terpisah sebelum production.

## Tiga Lapisan Perlindungan

1. **Railway volume backup:** aktifkan daily, weekly, dan monthly backup pada volume MySQL untuk pemulihan cepat di platform.
2. **Cloudinary automatic backup:** aktifkan automatic backup, lalu jalankan **Back Up Existing Assets** satu kali untuk media yang sudah ada.
3. **Backup database admin:** maksimal setiap 72 jam, buka `/admin/backup-recovery`, pilih **Buat dan Unduh Backup**, lalu simpan ZIP AES-256 di penyimpanan lokal terenkripsi.

Backup Railway dan Cloudinary memakai kuota serta retensi layanan masing-masing. Periksa statusnya secara berkala melalui dashboard penyedia.

Referensi operasional resmi: [Railway Volume Backups](https://docs.railway.com/volumes/backups), [Cloudinary Backups and Version Management](https://cloudinary.com/documentation/backups_and_version_management), dan [MySQL mysqldump](https://dev.mysql.com/doc/refman/8.4/en/using-mysqldump.html).

## Penyimpanan Aman

- Gunakan passphrase unik minimal 16 karakter untuk setiap backup.
- Simpan passphrase di password manager, tidak di folder yang sama dengan file ZIP.
- Simpan sekitar 10 backup tiga-harian dan satu salinan bulanan selama 3 bulan.
- Hapus arsip yang melewati retensi dengan prosedur penghapusan aman perangkat.
- Jangan memasukkan `APP_KEY`, kredensial Railway, kredensial Cloudinary, atau password database ke folder backup.
- Simpan recovery kit berisi secret dan akses penyedia secara terpisah di password manager organisasi.

## Membuat Backup Database

1. Pastikan tidak ada backup lain yang sedang berjalan.
2. Login sebagai admin dan buka **Administrasi > Backup & Pemulihan**.
3. Masukkan password akun admin.
4. Buat passphrase unik, konfirmasi, lalu simpan di password manager.
5. Unduh arsip dan catat checksum yang tampil pada riwayat.
6. Pindahkan arsip ke penyimpanan terenkripsi. Jangan menyimpan permanen di folder Downloads.

Sistem memakai `mysqldump --single-transaction --quick --no-tablespaces`. Session, cache, queue, failed jobs, dan token reset password tidak disertakan. File SQL plaintext dihapus setelah arsip AES-256 selesai dibuat.

## Memvalidasi Arsip

Dashboard dapat memvalidasi arsip melalui **Ajukan Restore**. Validasi memeriksa enkripsi AES-256, passphrase, struktur, manifest, checksum SQL, checksum ZIP, dan kecocokan dengan riwayat backup aplikasi. File upload langsung dihapus setelah pemeriksaan.

Validasi CLI tanpa menjalankan SQL:

```bash
php artisan backup:inspect /path/backup-file.zip
```

Tidak ada command atau action dashboard yang menjalankan SQL restore.

## Prosedur Restore Aktual

1. Nyatakan insiden dan batasi perubahan data bila diperlukan.
2. Buat backup darurat database aktif sebelum tindakan apa pun.
3. Siapkan database MySQL staging yang terpisah dari production.
4. Dekripsi arsip secara lokal pada workstation administrator yang aman.
5. Restore `database.sql` hanya ke database staging.
6. Jalankan pemeriksaan migration, jumlah record inti, foreign key, akun, UMKM, produk, submission, notifikasi, dan referensi media.
7. Tinjau permintaan anonimisasi/penghapusan yang terjadi setelah tanggal backup; terapkan kembali sebelum data boleh dipakai.
8. Dapatkan persetujuan administrator kedua jika tersedia.
9. Aktifkan maintenance mode, hentikan penulisan, lalu lakukan perpindahan database dengan prosedur penyedia.
10. Cabut seluruh session dan token reset password setelah restore.
11. Jalankan smoke test publik dan panel admin, lalu nonaktifkan maintenance mode.
12. Catat waktu, pelaksana, alasan, sumber backup, hasil verifikasi, dan tindak lanjut insiden.

## Latihan dan Pemeriksaan Berkala

- Jalankan latihan restore ke database lokal/staging minimal setiap 3 bulan.
- Periksa status backup Railway dan Cloudinary setiap bulan.
- Jalankan `php artisan backup:cleanup` melalui scheduler agar arsip server sementara yang tertinggal dihapus maksimal satu jam.
- Jika scheduler production belum tersedia, jalankan command cleanup setiap jam melalui Railway Cron service.
- Uji checksum dan passphrase untuk sampel backup; jangan menunggu insiden untuk mengetahui arsip rusak.

## Rollback dan Kegagalan

- Jika pembuatan ZIP AES-256 gagal, jangan menyimpan dump plaintext.
- Jika cleanup gagal, batasi akses host dan jalankan `php artisan backup:cleanup` segera.
- Jika restore staging tidak konsisten, hentikan proses dan gunakan backup lain. Jangan mencoba langsung di production.
- Jika backup Cloudinary tidak aktif, jangan menganggap ZIP database melindungi media.
