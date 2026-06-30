# Web App Flow

## Flow Public User

1. User membuka homepage.
2. User dapat memakai skip link untuk langsung menuju konten utama jika menavigasi dengan keyboard.
3. Jika pertama kali berkunjung, user melihat pemberitahuan privasi ringan dan bisa membaca `/kebijakan-privasi`.
4. Jika pertama kali menggunakan website, user juga dapat melihat interactive walkthrough dan bisa melewati kapan saja.
5. User mencari produk, jasa, kategori, lokasi/RW, atau nama UMKM melalui search utama di navbar.
6. User melihat listing UMKM atau produk/jasa dengan hasil yang diumumkan melalui live region.
7. User membuka detail UMKM atau detail produk.
8. User menghubungi pemilik usaha lewat WhatsApp, telepon, maps, website, atau media sosial.
9. Transaksi dilakukan langsung di luar website.

## Flow Informasi Publik

1. User membuka `/tentang` untuk memahami tujuan Cimuning Digital Hub, cara kerja direktori, status verified, QR profil, Maps, dan prinsip kontak langsung.
2. User membuka `/kontak`, tombol bantuan mengambang, atau link Bantuan untuk memilih jalur bantuan dan menghubungi pengelola melalui email/WhatsApp resmi.
3. User membuka `/kebijakan-privasi` untuk memahami data yang dikelola, data yang tampil publik, layanan pihak ketiga, hak pengguna, dan kanal bantuan.
4. Halaman kontak tidak menyimpan pesan ke database; WhatsApp dan email dibuka melalui layanan eksternal.
5. Kontak resmi sementara adalah `cimuningppk@gmail.com` dan WhatsApp `0878-0405-4071`; percakapan tidak dilacak oleh aplikasi.
6. User dapat membaca `/syarat-penggunaan` untuk memahami kewajiban owner, konten terlarang, moderasi, keamanan akun, dan transaksi langsung.

## Flow Privasi dan Persetujuan

1. Visitor publik melihat pemberitahuan privasi first-visit yang tidak menghalangi akses website.
2. Sistem menyimpan status pemberitahuan di browser dengan localStorage key `cimuning_privacy_notice_seen_v1`.
3. Visitor dapat membaca `/kebijakan-privasi` kapan saja dari navbar/drawer, footer, atau halaman kontak.
4. Calon owner wajib menyetujui Kebijakan Privasi dan Syarat Penggunaan melalui checkbox terpisah saat membuat akun di `/admin/register`.
5. Sistem menyimpan timestamp dan versi `2026-06-29` untuk kedua persetujuan; owner lama tidak dipaksa menyetujui ulang.
6. Permintaan akses, koreksi, pembaruan, nonaktif, atau penghapusan data diarahkan ke kontak resmi pada `/kontak`.
7. Kebijakan perlu diperbarui jika nanti aplikasi menambah analytics, email marketing, chat, payment, checkout, atau tracking baru.

## Flow UMKM Owner

1. Calon owner membuka `/daftar-umkm`.
2. Owner membuat akun melalui `/admin/register`, menyetujui Kebijakan Privasi serta Syarat Penggunaan, dan menjawab CAPTCHA lokal sederhana.
3. Sistem membuat akun dengan role `umkm_owner` dan mengarahkan owner ke dashboard.
4. Owner mengisi atau memperbarui profil UMKM melalui wizard Informasi usaha, Kontak, Lokasi, Media sosial, Foto & layanan, dan Konfirmasi.
5. Owner tidak perlu memahami slug/URL teknis karena sistem membuat slug otomatis.
6. Owner wajib memilih RW 01-RW 26 melalui dropdown pencarian, lalu mengisi alamat dan dapat memakai Geolocation atau link Google Maps yang berisi koordinat.
7. Owner mengecek titik Google Maps yang tersimpan; alamat tertulis tetap diisi manual karena sistem tidak memakai reverse geocoding.
8. UMKM baru tersimpan sebagai pending dan belum tampil publik.
9. Owner menambah produk/jasa dan foto setelah profil UMKM tersedia.
10. Owner melihat status pending, verified, rejected, atau need revision.
11. Owner memperbaiki data jika admin meminta revisi.

## Flow Admin

1. Admin login ke dashboard.
2. Admin membuka menu `Verifikasi UMKM` dan memilih submission pending.
3. Admin memeriksa profil, kontak, kategori, foto, lokasi, layanan, serta perbandingan perubahan secara read-only.
4. Admin memilih Verifikasi, Minta revisi, atau Tolak; alasan wajib untuk revisi dan penolakan.
5. Admin tidak mengubah data owner. Koreksi dilakukan owner melalui pengajuan berikutnya.
6. Admin tetap mengelola kategori, kurasi UMKM pilihan, blokir publikasi UMKM, dan blokir produk bermasalah melalui action terpisah yang tercatat.
7. Admin mengelola akun owner yang sudah mendaftar melalui menu `Akun Owner`; akun admin dan role tidak dapat diubah dari dashboard.
8. Admin tidak melihat atau menentukan password. Link reset hanya dapat dikirim bila mail production dan `AUTH_PASSWORD_RESET_ENABLED` sudah aktif.
9. Login/logout admin, kegagalan login panel, akses sensitif yang ditolak, perubahan profil admin, dan CRUD kategori masuk ke `Log Aktivitas Admin`.

## Flow Administrasi Akun Owner

1. Owner membuat akun sendiri melalui `/admin/register`; admin tidak membuat akun atau password sementara.
2. Owner dapat memperbarui nama, email, dan password miliknya melalui halaman `Profil & Keamanan Akun` Filament.
3. Perubahan email atau password mewajibkan password saat ini. Pemulihan lupa password baru diaktifkan setelah SMTP production tersedia.
4. Bila akses perlu dihentikan, admin menangguhkan akun dengan alasan wajib; seluruh session dan token reset dicabut tanpa otomatis mengubah publikasi UMKM.
5. Admin dapat mengaktifkan kembali akun suspended dengan catatan keputusan.
6. Koreksi nama/email oleh admin hanya dilakukan atas permintaan yang sah dan selalu dicatat dalam log.
7. Permintaan penghapusan yang telah diverifikasi diproses melalui anonimisasi setelah akun disuspend.
8. Anonimisasi membersihkan identitas, kontak, media, dan payload data pribadi; keputusan audit minimum tetap read-only.
9. Jika cleanup media gagal, akun tetap `anonymization_pending` dan proses dapat dicoba ulang tanpa mengaktifkan akses kembali.

## Flow Dashboard Owner

1. Owner login ke `/admin` dan melihat ringkasan yang hanya mengambil UMKM serta produk miliknya.
2. Card `Status Profil UMKM` menunjukkan apakah profil belum lengkap, menunggu review, perlu revisi, terverifikasi, atau dinonaktifkan.
3. Card `Produk/Jasa` dan `Tampil Publik` membedakan seluruh isi katalog owner dari produk yang benar-benar dapat ditemukan masyarakat.
4. Card `Perlu Tindakan` hanya menghitung pekerjaan yang dapat dilakukan owner, seperti revisi profil atau perbaikan produk terblokir yang belum diajukan ulang.
5. Aksi cepat mengarah ke profil UMKM, katalog produk, profil publik bila sudah tayang, keamanan akun, dan bantuan resmi.
6. Statistik global platform seperti jumlah kategori aktif hanya ditampilkan kepada admin.

## Flow Pencarian UMKM

1. User membuka `/umkm` atau masuk dari kategori tertentu.
2. Halaman langsung menampilkan filter dan daftar UMKM tanpa hero pengantar atau search box lokal.
3. User memakai search utama di navbar untuk mengganti kata kunci; pada halaman UMKM navbar mengarah ke `/umkm?search=...`.
4. User dapat memfilter berdasarkan kategori, lokasi/RW, verified, layanan, urutan, dan jumlah per halaman.
5. Setiap pilihan filter langsung membuka URL GET baru sehingga hasil dan query string ikut diperbarui.
6. Filter aktif tampil sebagai chip dan bisa dihapus satu per satu tanpa menghapus semua pencarian.
7. Nilai filter yang tidak valid dikembalikan ke default agar URL aneh tidak merusak halaman.
8. Total hasil, loading, dan empty state tersedia sebagai live region untuk assistive technology.
9. Jika tidak ada hasil, tampilkan empty state yang ramah dengan aksi hapus filter atau cari produk/jasa.

## Flow Pendaftaran UMKM

1. User membuka `/daftar-umkm`.
2. User membaca manfaat dan langkah pendaftaran account-first.
3. User membuat akun owner dari `/admin/register` dengan persetujuan Kebijakan Privasi dan Syarat Penggunaan secara terpisah, CAPTCHA matematika lokal tokenized, dan honeypot anti-spam.
4. Setelah login, owner melengkapi data usaha, kategori, kontak, lokasi, media sosial, layanan, dan foto dari dashboard.
5. Sistem membantu lokasi tanpa API berbayar: browser Geolocation, parsing koordinat/teks Maps, tombol cek titik tersimpan, dan tombol buka Google Maps.
6. Jika link Maps tidak berisi koordinat yang bisa dibaca, sistem meminta owner menempel URL Maps lengkap atau koordinat; link pendek tidak di-resolve server-side.
7. Sistem menerima Instagram/TikTok sebagai username atau URL, lalu menormalisasi saat data disimpan.
8. Sistem memvalidasi input dan upload.
9. Sistem membuat slug unik otomatis dan menyimpan data dengan status pending serta belum aktif.
10. Sistem mengirim notifikasi dashboard ke admin.
11. Admin melakukan verifikasi dari dashboard.

## Flow Pencarian Produk/Jasa

1. User membuka `/produk` atau mencari dari navbar.
2. Halaman langsung menampilkan filter dan daftar produk tanpa jumbotron pengantar atau search box lokal.
3. User memakai search utama di navbar untuk mengganti kata kunci; secara default navbar mengarah ke `/produk?search=...`.
4. User dapat membuka filter untuk kategori, UMKM, harga, urutan, dan jumlah per halaman.
5. Setiap pilihan filter langsung membuka URL GET baru sehingga hasil dan query string ikut diperbarui.
6. Filter aktif tampil sebagai chip dan bisa dihapus satu per satu tanpa menghapus semua pencarian.
7. Nilai filter yang tidak valid dikembalikan ke default agar URL aneh tidak merusak halaman.
8. Filter kategori mencocokkan kategori produk atau kategori UMKM sebagai fallback bila produk belum punya kategori sendiri.
9. Filter harga membedakan produk dengan harga di atas 0 dan produk yang perlu menghubungi UMKM.
10. Hasil hanya menampilkan produk aktif dari UMKM active dan verified.
11. User membuka kartu produk untuk melihat detail produk di `/produk/{slug}`.

## Flow Detail Produk

1. User membuka detail produk dari homepage, `/produk`, atau katalog pada detail UMKM.
2. Sistem hanya menampilkan produk aktif yang tidak diblokir admin dan berasal dari UMKM verified + active.
3. User melihat foto utama, galeri, nama produk/jasa, kategori, harga jika tersedia, deskripsi lengkap, dan UMKM pemilik.
4. User dapat menekan `Tanya Produk` untuk membuka WhatsApp langsung dengan pesan produk.
5. User dapat membuka profil UMKM untuk melihat katalog lain, alamat, Maps, QR profil, dan kontak lengkap.
6. Detail produk adalah halaman katalog informasi; tidak ada cart, checkout, payment, ongkir, atau transaksi internal.

## Flow Homepage Product-Led

1. User membuka homepage.
2. User langsung melihat search besar di navbar sebagai navigasi utama discovery.
3. User melihat carousel jumbotron informatif berisi produk lokal, UMKM verified, akun owner, dan katalog digital.
4. User memilih ikon kategori cepat, termasuk "Lihat Semua" untuk membuka `/kategori`.
5. User melihat produk/jasa terbaru dan dapat membuka detail produk atau bertanya lewat WhatsApp.
6. CTA membuka WhatsApp atau Google Maps secara langsung tanpa tracking database.
7. Tidak ada cart, checkout, payment, ongkir, atau transaksi internal.

## Flow Semua Kategori

1. User menekan ikon atau link "Lihat Semua" pada kategori homepage.
2. Sistem membuka `/kategori`.
3. User melihat semua kategori aktif dalam grid ikon, deskripsi singkat, dan jumlah UMKM verified.
4. User memilih kategori.
5. Sistem membuka `/produk?category={slug}` untuk menampilkan produk/jasa sesuai kategori.

## Flow First-Visit Tutorial

1. Visitor baru membuka halaman publik yang memakai public layout.
2. Sistem mengecek localStorage `cimuning_walkthrough_seen_v1`.
3. Jika belum pernah melihat tutorial, modal/bottom sheet membuka interactive walkthrough.
4. User dapat memulai panduan, mencoba search produk/jasa, membuka kategori/produk, atau menuju pendaftaran UMKM.
5. User dapat memilih "Skip", "Selesai", atau tombol tutup kapan saja.
6. Setelah ditutup, sistem menyimpan status di browser agar walkthrough tidak muncul lagi.

## Flow Verifikasi UMKM

1. Owner mengirim profil baru atau perubahan profil sebagai submission.
2. Admin membuka halaman review khusus; tidak ada action keputusan cepat dari tabel UMKM.
3. Verifikasi membutuhkan checklist kelengkapan, kontak/lokasi, dan kepatuhan konten.
4. Minta revisi dan Tolak membutuhkan alasan yang dikirim ke owner.
5. Approval menerapkan snapshot owner secara atomik tanpa mengubah slug, owner, atau kurasi featured.
6. Untuk profil verified, versi publik lama tetap aktif selama perubahan baru menunggu review.
7. Owner dapat memperbaiki submission revisi/ditolak dan mengajukan ulang tanpa kehilangan riwayat keputusan.
8. Semua keputusan menyimpan reviewer, catatan, checklist, dan waktu review.

## Flow Notifikasi Dashboard

1. Admin atau owner login ke `/admin`.
2. Filament menampilkan notification bell dengan polling berkala.
3. Admin menerima notifikasi submission pendaftaran atau perubahan profil baru.
4. Owner menerima notifikasi ketika submission disetujui, perlu revisi, atau ditolak.
5. Notifikasi admin menuju halaman review read-only; notifikasi owner menuju form profil miliknya.
6. Notifikasi tahap MVP hanya tersimpan di database, tanpa email, WhatsApp, atau realtime broadcast.

## Flow Tambah Produk/Jasa

1. Owner membuka dashboard products.
2. Owner menambah nama, kategori, deskripsi, harga opsional, gambar utama, galeri foto opsional, dan status aktif.
3. Sistem memvalidasi data dan upload gambar JPG/PNG/WEBP maksimal 2 MB per file.
4. Livewire menyimpan upload sementara pada disk lokal; setelah validasi, file final masuk ke public storage lokal atau Cloudinary sesuai `FILESYSTEM_DISK`.
5. Transfer ke Cloudinary memakai stream multipart; browser menerima URL `f_auto/q_auto` tanpa thumbnail, resize, atau crop otomatis.
6. Endpoint temporary membatasi format, ukuran, dimensi, dan rate; adapter memeriksa ulang isi file sebelum upload.
7. Cloudinary delivery memakai signed URL untuk kompatibilitas dengan Strict Transformations.
8. Produk tampil di detail UMKM, listing produk, homepage, dan halaman detail produk jika aktif.
9. Jika gambar utama kosong, tampilan publik memakai foto galeri pertama sebagai fallback.
10. Admin tidak mengedit produk owner, tetapi dapat memblokir produk bermasalah dengan alasan dan audit; owner tidak dapat membuka blokir sendiri.
11. Setelah memperbaiki produk terblokir, owner memilih `Ajukan Peninjauan Ulang` dan wajib menjelaskan perbaikannya.
12. Sistem mencegah permintaan ganda, mencatat permintaan ke log moderasi, dan memberi notifikasi kepada admin.
13. Produk tetap tersembunyi dari homepage, listing produk, dan detail UMKM selama menunggu keputusan.
14. Admin meninjau produk secara read-only lalu membuka blokir atau menolak permintaan dengan alasan wajib.
15. Keputusan membersihkan status permintaan, dicatat dalam audit, dan dikirim kepada owner melalui notification bell.

## Flow Audit Administrasi dan Moderasi

1. Admin membuka menu `Log Moderasi` di panel `/admin`.
2. Log menampilkan lifecycle akun owner, kurasi featured, blokir publikasi UMKM, blokir produk, permintaan review owner, penolakan review, dan pembukaan blokir.
3. Admin dapat menyaring log berdasarkan aksi, aktor, jenis konten, dan rentang waktu.
4. Log bersifat read-only dan tidak dapat dibuat, diubah, atau dihapus dari panel.
5. Owner tidak dapat membuka resource audit dan tidak dapat mengubah field moderasi secara langsung.

## Flow Audit Keamanan Admin

1. Admin membuka menu `Log Aktivitas Admin` di panel `/admin`.
2. Sistem mencatat login/logout admin, login panel gagal, akses sensitif yang ditolak, perubahan profil admin, dan create/update/delete kategori.
3. Setiap record menyimpan waktu, aktor bila diketahui, target, request ID, route, method, dan perubahan field non-sensitif.
4. Password, token, secret, IP mentah, query pencarian, cookie, dan isi media tidak disimpan dalam audit.
5. Login gagal menyimpan hash identitas untuk korelasi tanpa menyimpan email percobaan sebagai teks terbuka.
6. Log bersifat read-only dari dashboard; perubahan langsung oleh pemegang akses database tetap berada di luar jangkauan audit aplikasi.

## Flow Kontak WhatsApp/Maps

1. User membuka detail UMKM.
2. User melihat alamat tertulis dan titik Google Maps sebagai informasi lokasi yang berbeda.
3. User menekan CTA WhatsApp atau Lihat Lokasi.
4. Website membuka WhatsApp atau Google Maps secara langsung tanpa route perantara dan tanpa menyimpan event.
5. Transaksi tetap dilakukan langsung di luar website.

## Flow SEO Public Discovery

1. Search engine atau user membuka halaman public.
2. Layout public menyediakan canonical URL, meta description, Open Graph, dan Twitter card.
3. Detail UMKM verified menyediakan JSON-LD `LocalBusiness` dari data usaha, lokasi, kontak, dan katalog produk.
4. `/sitemap.xml` memuat halaman public, Kebijakan Privasi, Syarat Penggunaan, kategori aktif, UMKM aktif + verified, dan produk public.
5. `/admin`, UMKM pending/rejected/inactive, dan fitur transaksi tidak masuk sitemap.

## Flow QR Profil UMKM

1. User membuka detail UMKM verified.
2. Halaman menampilkan QR profil yang mengarah langsung ke URL profil publik.
3. User dapat scan atau download SVG QR untuk dibagikan offline.
4. Scan QR tidak dicatat atau disimpan di database.
5. QR hanya aktif untuk UMKM yang sudah verified dan active.

## Flow Deployment Railway

1. Perubahan project dipush ke GitHub branch yang dipantau Railway.
2. Railway membangun container dari `Dockerfile`.
3. Build menginstall dependency PHP, dependency Node, dan menjalankan build Vite.
4. Saat container start, `docker-entrypoint.sh` membersihkan bootstrap cache, membuat storage link, menjalankan migration, melewati seeder kecuali diaktifkan eksplisit, mengoptimalkan Laravel, lalu menyalakan FrankenPHP.
5. FrankenPHP/Caddy hanya menyajikan folder `/public`; request Livewire dan Filament diteruskan ke Laravel melalui `php_server`.
6. Scheduler hosting menjalankan `schedule:run` setiap menit untuk heartbeat dan cleanup backup.
7. Operator menjalankan `app:production-check --with-external --require-scheduler` sebelum membuka akses masyarakat.
6. Railway reverse proxy menerima HTTPS dari browser dan meneruskan ke container; Laravel memaksa URL HTTPS dan mempercayai proxy agar asset tidak mixed content.
7. Database production testing memakai Railway MySQL.
8. Upload logo, cover, dan foto produk memakai temporary disk lokal lalu Cloudinary sebagai storage permanen saat `FILESYSTEM_DISK=cloudinary`.
9. Local development tetap bisa berjalan dengan XAMPP/MySQL lokal dan disk lokal/public sesuai `.env`.

## Flow Backup dan Pemulihan

1. Admin membuka `Backup Data`; owner dan guest tidak memiliki akses.
2. Admin memasukkan password akun dan passphrase unik minimal 16 karakter.
3. Sistem menjalankan dump MySQL konsisten, mengecualikan data sementara, lalu membuat ZIP AES-256 berisi `database.sql` dan manifest checksum.
4. SQL plaintext dan file kredensial sementara selalu dihapus; passphrase tidak disimpan di database maupun audit.
5. Admin mengunduh arsip dan menyimpannya pada penyimpanan terenkripsi, terpisah dari passphrase.
6. Action `Ajukan Restore` hanya memvalidasi arsip dan mencatat permintaan. Dashboard tidak memiliki kemampuan menjalankan SQL.
7. Restore aktual dilakukan ke database staging, diverifikasi, lalu dipindahkan melalui maintenance procedure sesuai `docs/BACKUP_RESTORE_RUNBOOK.md`.
8. Media dilindungi terpisah melalui Cloudinary automatic backup; database Railway dilindungi pula dengan backup volume daily, weekly, dan monthly.
