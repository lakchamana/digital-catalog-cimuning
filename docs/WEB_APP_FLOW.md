# Web App Flow

## Flow Public User

1. User membuka homepage.
2. User dapat memakai skip link untuk langsung menuju konten utama jika menavigasi dengan keyboard.
3. Jika pertama kali berkunjung, user melihat interactive walkthrough dan bisa melewati kapan saja.
4. User mencari produk, jasa, kategori, lokasi/RW, atau nama UMKM melalui search utama di navbar.
5. User melihat listing UMKM atau produk/jasa dengan hasil yang diumumkan melalui live region.
6. User membuka detail UMKM.
7. User menghubungi pemilik usaha lewat WhatsApp, telepon, maps, website, atau media sosial.
8. Transaksi dilakukan langsung di luar website.

## Flow Informasi Publik

1. User membuka `/tentang` untuk memahami tujuan Cimuning Digital Hub, cara kerja direktori, status verified, QR profil, Maps, dan prinsip kontak langsung.
2. User membuka `/kontak` atau link “Bantuan” untuk memilih jalur bantuan: cari produk/jasa, lihat UMKM, daftar owner, login owner, verifikasi, atau revisi data.
3. Halaman kontak v1 tidak menampilkan nomor/email dummy dan tidak menyimpan pesan ke database.
4. Kontak resmi pengelola dapat ditambahkan setelah kanal resmi final.

## Flow UMKM Owner

1. Calon owner membuka `/daftar-umkm`.
2. Owner membuat akun melalui `/admin/register` dan menjawab CAPTCHA lokal sederhana.
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
6. Admin tetap mengelola kategori, kurasi UMKM pilihan, dan blokir produk bermasalah melalui action terpisah yang tercatat.

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
3. User membuat akun owner dari `/admin/register` dengan CAPTCHA matematika lokal tokenized dan honeypot anti-spam.
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

## Flow Audit Moderasi Admin

1. Admin membuka menu `Log Moderasi` di panel `/admin`.
2. Log menampilkan kurasi featured, blokir produk, permintaan review owner, penolakan review, dan pembukaan blokir.
3. Admin dapat menyaring log berdasarkan aksi, aktor, jenis konten, dan rentang waktu.
4. Log bersifat read-only dan tidak dapat dibuat, diubah, atau dihapus dari panel.
5. Owner tidak dapat membuka resource audit dan tidak dapat mengubah field moderasi secara langsung.

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
4. `/sitemap.xml` memuat halaman public, kategori aktif, dan UMKM aktif + verified.
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
4. Saat container start, `docker-entrypoint.sh` membersihkan cache lama, membuat config/route cache baru dari environment variables Railway, membuat storage link, menjalankan migration, menjalankan seeder idempotent, lalu menyalakan PHP server.
5. `server.php` menjadi router PHP built-in server: file statis yang ada diserve langsung, sedangkan request asset/route Livewire dan Filament yang bukan file fisik diteruskan ke Laravel.
6. Railway reverse proxy menerima HTTPS dari browser dan meneruskan ke container; Laravel memaksa URL HTTPS dan mempercayai proxy agar asset tidak mixed content.
7. Database production testing memakai Railway MySQL.
8. Upload logo, cover, dan foto produk memakai temporary disk lokal lalu Cloudinary sebagai storage permanen saat `FILESYSTEM_DISK=cloudinary`.
9. Local development tetap bisa berjalan dengan XAMPP/MySQL lokal dan disk lokal/public sesuai `.env`.
