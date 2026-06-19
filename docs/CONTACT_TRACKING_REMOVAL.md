# Contact Tracking Removal Runbook

Dokumen ini menjadi panduan deployment penghapusan fitur tracking kontak dari Cimuning Digital Hub. Setelah proses selesai, aplikasi tidak menyimpan klik WhatsApp, klik Google Maps, scan QR, IP, user agent, atau referer pengunjung.

## Perubahan Yang Dideploy

- Route `/leads/{umkm}/{type}` dan `/qr/umkm/{umkm}/open` dihapus.
- CTA WhatsApp dan Maps membuka URL eksternal secara langsung.
- QR SVG mengarah langsung ke `/umkm/{slug}`.
- Model, controller, recorder, relasi Eloquent, dan widget analytics tracking dihapus.
- Migration `2026_06_19_000001_drop_lead_events_table` menghapus tabel `lead_events`.

## Langkah Deployment Railway

1. Pastikan perubahan sudah dipush ke branch GitHub yang dipantau Railway.
2. Jika data tracking lama perlu diarsipkan untuk alasan operasional, ekspor sebelum deploy. Lewati langkah ini jika data memang harus dibuang permanen.
3. Deploy revision terbaru ke Railway.
4. Periksa deployment log. `docker-entrypoint.sh` harus menjalankan `php artisan migrate --force` dan menampilkan migration `2026_06_19_000001_drop_lead_events_table` sebagai berhasil.
5. Jalankan `php artisan migrate:status` di Railway shell dan pastikan migration penghapusan berstatus `Ran`.
6. Verifikasi database MySQL dengan `SHOW TABLES LIKE 'lead_events';`. Hasil harus kosong.
7. Jalankan `php artisan route:list --except-vendor`. Tidak boleh ada route dengan path `/leads/` atau `/qr/.../open`.
8. Smoke test halaman homepage, `/produk`, `/umkm`, dan satu detail UMKM verified.
9. Pastikan tombol WhatsApp membuka `wa.me`, tombol Maps membuka Google Maps, dan QR membuka profil UMKM langsung.
10. Buka `/admin` sebagai admin dan owner. Widget statistik/aktivitas kontak tidak boleh muncul.

## Verifikasi Setelah Deploy

- Tidak ada tabel `lead_events`.
- Tidak ada request aplikasi ke `/leads/...`.
- Tidak ada widget analytics kontak di dashboard.
- WhatsApp, Maps, QR, listing, detail UMKM, dan katalog produk tetap berfungsi.
- Log aplikasi tidak menampilkan error class atau route tracking yang sudah dihapus.

## Catatan Rollback

Migration penghapusan sengaja tidak membuat ulang tabel pada method `down()`. Rollback source code tidak boleh dipakai untuk mengaktifkan tracking kembali. Jika keputusan produk berubah di masa depan, fitur analytics harus dirancang ulang melalui migration dan persetujuan privasi yang baru.
