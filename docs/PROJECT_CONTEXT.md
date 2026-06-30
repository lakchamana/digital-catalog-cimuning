# Project Context

## Ringkasan

Cimuning Digital Hub adalah web app katalog online untuk membantu masyarakat menemukan UMKM lokal di Cimuning, Kota Bekasi. Platform ini berfungsi sebagai etalase digital dan direktori usaha yang mengintegrasikan katalog produk digital, lokasi Google Maps, dan jalur kontak langsung ke pelaku usaha.

## Jenis Platform

Platform ini adalah online directory/katalog digital. Fokus utama aplikasi adalah pencarian UMKM, profil usaha, produk/jasa, status verifikasi, lokasi Google Maps, dan jalur kontak langsung seperti WhatsApp, telepon, website, dan media sosial.

CTA WhatsApp dan Google Maps mengarah langsung ke layanan eksternal, sedangkan QR mengarah langsung ke profil UMKM. Platform tidak menyimpan event klik, scan QR, IP, user agent, referer, atau analytics kontak di database.

## Privasi dan Data Pribadi

Kebijakan Privasi publik tersedia di `/kebijakan-privasi` dan ditulis agar mudah dipahami oleh visitor, owner UMKM, dan pengelola. Substansinya mengikuti prinsip transparansi UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi: jelaskan data yang dikumpulkan, tujuan pemrosesan, data yang tampil publik, pihak ketiga, hak pengguna, keamanan, retensi, dan kanal bantuan.

Syarat Penggunaan tersedia di `/syarat-penggunaan` untuk mengatur kebenaran data owner, hak atas konten, produk terlarang, keamanan akun, moderasi, penangguhan, layanan pihak ketiga, dan transaksi langsung di luar platform.

Visitor publik melihat pemberitahuan privasi first-visit yang ringan, bukan banner "accept cookies", karena aplikasi tidak memakai analytics/tracking cookies. Status pemberitahuan disimpan di browser memakai localStorage key `cimuning_privacy_notice_seen_v1`.

Owner baru wajib menyetujui Kebijakan Privasi dan Syarat Penggunaan secara terpisah ketika membuat akun di `/admin/register`. Persetujuan versi `2026-06-29` dicatat pada kolom privacy/terms user. Owner lama tetap dapat login tanpa persetujuan paksa. Data owner yang dapat tampil publik meliputi profil UMKM verified, produk aktif, kontak usaha, alamat/RW, titik Maps, media sosial, QR profil, serta foto/logo/cover/galeri yang owner unggah.

Kebijakan Privasi dan Syarat Penggunaan perlu review hukum formal sebelum peluncuran luas serta harus diperbarui jika model layanan atau pemrosesan data berubah. Kontak resmi sementara adalah `cimuningppk@gmail.com` dan WhatsApp `0878-0405-4071`, dipusatkan di `config/support.php`.

## Target User

- Public user atau warga yang mencari produk, jasa, toko, atau nama UMKM.
- UMKM owner yang ingin mempromosikan profil usaha dan produk/jasanya.
- Admin/pengelola yang meninjau pengajuan UMKM, mengelola kategori, dan menjalankan moderasi tanpa menulis ulang data owner.

## Tidak Ada Payment Gateway

Website ini tidak menangani pembayaran, checkout, cart, ongkir otomatis, atau transaksi uang. Semua transaksi dilakukan langsung antara calon pembeli dan pemilik UMKM di luar website. Keputusan ini membuat MVP lebih aman, sederhana, dan sesuai fungsi direktori lokal.

## Stack

- Backend: Laravel.
- Database: MySQL pada fase implementasi data.
- Frontend: Blade dan Tailwind CSS.
- Interactivity: Livewire untuk search/filter/pagination/form data, Alpine.js untuk drawer, modal, dropdown, dan hamburger menu.
- Auth/back office: Laravel authentication system melalui Laravel Filament 5 untuk panel `/admin`.
- Deployment testing: Railway web service dengan Docker, Railway MySQL, dan Cloudinary untuk file upload production.

## Prinsip Desain

Arah visual utama adalah Local Directory, Clean Marketplace, Community Trust, dan Mobile Friendly. Sumber utama desain adalah `DESIGN-CIMUNING-UMKM-DIRECTORY.md`, dengan inspirasi struktur katalog dari Indonetwork dan whitespace/card discovery dari Bridestory.

Filosofi warna: Merah Semangat, Putih Terbuka, Hijau Tumbuh, Biru Terpercaya.

- Merah untuk CTA utama dan identitas.
- Putih hangat untuk background utama.
- Hijau untuk verified/aktif.
- Biru untuk link, maps, dan action sekunder.
- Hijau WhatsApp khusus tombol WhatsApp.

## Prinsip Mobile-First

Mayoritas user diasumsikan memakai smartphone. Semua halaman harus nyaman digunakan di layar kecil, search mudah ditemukan, card mudah dibaca, CTA WhatsApp jelas, tombol minimal 44px, body text minimal 16px, dan tabel lebar harus dihindari di mobile.

## Role User

- Admin: mengelola kategori dan lifecycle akun owner, meninjau submission secara read-only, melakukan kurasi featured, serta memoderasi publikasi UMKM/produk dengan alasan tercatat. Admin tidak melihat password, mengubah role, atau menulis ulang konten owner.
- UMKM Owner: mengelola profil dan produk miliknya sendiri.
- Public User/Guest: melihat homepage, mencari UMKM, melihat produk/jasa, dan menghubungi UMKM tanpa login.

## Dashboard Back Office

Panel `/admin` memakai Laravel Filament 5. Public pages tetap Blade + Livewire dan tidak dipindahkan ke Filament. Owner mengelola profil serta produknya sendiri. Admin melihat data owner secara read-only dan mengambil keputusan melalui resource `Verifikasi UMKM`; koreksi konten harus dilakukan owner. Perubahan profil verified disimpan sebagai draft submission sehingga versi publik lama tetap tayang sampai perubahan disetujui.

Moderasi produk mengikuti batas tanggung jawab yang sama. Admin dapat memblokir produk dengan alasan, tetapi tidak mengubah konten owner. Setelah memperbaiki produk, owner mengajukan peninjauan ulang dengan catatan; admin kemudian membuka blokir atau menolak permintaan. Produk tetap tidak tampil publik sampai blokir benar-benar dicabut. Seluruh lifecycle akun, featured, blokir UMKM, dan moderasi produk tercatat pada resource read-only `Log Moderasi`.

Resource `Akun Owner` hanya menampilkan akun role `umkm_owner`. Admin dapat melakukan koreksi identitas atas permintaan, suspend, reaktivasi, dan anonimisasi terkontrol. Suspend hanya menghentikan akses, sedangkan publikasi UMKM dikendalikan oleh action moderasi terpisah. Owner mendaftar sendiri dan dapat mengelola nama, email, serta password melalui profile page Filament.

Dashboard owner tidak memakai statistik global admin. Ringkasannya berfokus pada status profil UMKM, seluruh produk milik owner, produk yang benar-benar tampil publik, dan pekerjaan yang perlu ditindaklanjuti. Aksi cepat mengarah ke pengelolaan profil, katalog, profil publik, serta halaman keamanan akun. Admin tetap melihat statistik platform termasuk kategori aktif.

Audit admin dibagi menjadi dua: `Log Moderasi` untuk keputusan konten/lifecycle owner dan `Log Aktivitas Admin` untuk autentikasi admin, akses sensitif yang ditolak, perubahan profil admin, serta CRUD kategori. Log aktivitas menggunakan request ID dan safe before/after diff tanpa password, token, secret, IP mentah, query string, atau binary media. Percobaan login memakai hash identitas, bukan email mentah.

Owner yang masih login dapat mengganti password melalui halaman `Profil & Keamanan Akun` dengan memasukkan password saat ini. Reset password email tersedia secara conditional melalui `AUTH_PASSWORD_RESET_ENABLED`. Railway private/testing mempertahankan nilai `false`; hosting publik wajib menyediakan SMTP/domain pengirim dan menguji delivery sebelum mengaktifkannya.

Form UMKM owner menggunakan wizard dengan bahasa publik yang sederhana. RW wajib dipilih dari `RW 01` sampai `RW 26`; slug dan koordinat mentah ditangani sistem, sedangkan status publik serta featured hanya berubah melalui workflow admin yang terkontrol. Aplikasi tidak menyimpan jumlah kunjungan profil atau menyediakan sort popularitas berbasis tracking.

Upload gambar di lokal memakai public disk Laravel dan storage link `public/storage`. Di production Railway, Livewire menyimpan file sementara pada disk lokal container lalu Filament memindahkan hasil validasi ke Cloudinary melalui custom filesystem disk. URL media publik selalu dibentuk dari disk aktif. Gambar yang didukung adalah JPG, PNG, dan WEBP dengan batas 2 MB.

Transfer final ke Cloudinary menggunakan multipart stream tanpa Base64. Delivery Cloudinary memakai format dan kualitas otomatis (`f_auto/q_auto`) tanpa mengubah ukuran, crop, atau rasio gambar.

Upload media memiliki defense-in-depth: temporary validation, throttle 20/menit, pemeriksaan MIME/ukuran/path di adapter, dan signed delivery URL. Cloudinary Strict Transformations diaktifkan manual setelah signed URL production terverifikasi.

Backup database aplikasi tersedia sebagai ZIP AES-256 yang dibuat admin maksimal setiap 72 jam. Backup volume Railway dan automatic backup Cloudinary adalah opsi tambahan selama deployment internal, bukan ketergantungan fitur. Saat hosting production final dipilih, kebijakan backup database dan media harus disesuaikan dengan fasilitas penyedia tersebut. Dashboard hanya memvalidasi dan mencatat permintaan restore; eksekusi SQL langsung dari web sengaja tidak tersedia. Prosedur lengkap ada di `docs/BACKUP_RESTORE_RUNBOOK.md`.

## Deployment

Project sudah disiapkan untuk testing internal di Railway pada URL production `https://digital-catalog-cimuning-production.up.railway.app/`. Railway menjalankan container Docker berbasis PHP 8.3/FrankenPHP, build Vite dengan Node.js 22, memakai Railway MySQL Plugin, dan menyimpan upload UMKM/produk ke Cloudinary.

File deployment penting:

- `Dockerfile` untuk build container Railway.
- `docker-entrypoint.sh` untuk runtime cache, storage link, migrate, seed, dan start server.
- `server.php` sebagai router PHP built-in server agar route Livewire/Filament JS tetap diteruskan ke Laravel.
- `config/cloudinary.php` dan `App\Support\CloudinaryStorage` untuk filesystem disk Cloudinary.

Cache config/route dilakukan saat runtime di `docker-entrypoint.sh`, bukan saat Docker build, karena environment variables Railway baru tersedia saat container berjalan.

## Fitur MVP

- Homepage.
- Listing UMKM dengan search dan filter.
- Detail UMKM.
- Listing produk/jasa.
- Listing kategori.
- Pendaftaran UMKM.
- Dashboard admin/owner.
- CRUD kategori, UMKM, produk/jasa.
- Upload foto.
- Status verifikasi.
- QR profil UMKM untuk offline sharing.

## Fitur Ditunda

- Payment gateway.
- Checkout.
- Cart/keranjang.
- Ongkir otomatis.
- Chat realtime internal.
- Review/rating kompleks.
- Mobile app native.
- Multi-vendor transaction system.
- Tracking klik WhatsApp/Maps atau scan QR.

## Catatan Untuk AI Berikutnya

Jangan mengubah arah platform menjadi e-commerce dan jangan menambahkan kembali tracking kontak/QR tanpa keputusan produk baru. Prioritaskan search, direktori, profil UMKM, mobile-first UI, keamanan role, validasi form, dan kontak langsung ke UMKM. Update `docs/CHANGELOG.md` dan `docs/AI_HANDOFF_NOTES.md` setiap selesai task besar.
