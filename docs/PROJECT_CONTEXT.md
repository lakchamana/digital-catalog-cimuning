# Project Context

## Ringkasan

Cimuning Digital Hub adalah web app katalog online untuk membantu masyarakat menemukan UMKM lokal di Cimuning, Kota Bekasi. Platform ini berfungsi sebagai etalase digital dan direktori usaha yang mengintegrasikan katalog produk digital, lokasi Google Maps, dan jalur kontak langsung ke pelaku usaha.

## Jenis Platform

Platform ini adalah online directory/katalog digital. Fokus utama aplikasi adalah pencarian UMKM, profil usaha, produk/jasa, status verifikasi, lokasi Google Maps, dan jalur kontak langsung seperti WhatsApp, telepon, website, dan media sosial.

CTA WhatsApp dan Google Maps mengarah langsung ke layanan eksternal, sedangkan QR mengarah langsung ke profil UMKM. Platform tidak menyimpan event klik, scan QR, IP, user agent, referer, atau analytics kontak di database.

## Target User

- Public user atau warga yang mencari produk, jasa, toko, atau nama UMKM.
- UMKM owner yang ingin mempromosikan profil usaha dan produk/jasanya.
- Admin/pengelola yang memverifikasi dan mengelola data UMKM.

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

- Admin: mengelola kategori, UMKM, produk/jasa, foto, dan status verifikasi.
- UMKM Owner: mengelola profil dan produk miliknya sendiri.
- Public User/Guest: melihat homepage, mencari UMKM, melihat produk/jasa, dan menghubungi UMKM tanpa login.

## Dashboard Back Office

Panel `/admin` memakai Laravel Filament 5. Public pages tetap Blade + Livewire dan tidak dipindahkan ke Filament. Admin dapat mengelola kategori, semua UMKM, semua produk, upload foto, dan status verifikasi. UMKM owner dapat masuk panel tetapi query resource dibatasi ke UMKM dan produk miliknya sendiri.

Form UMKM owner menggunakan wizard dengan bahasa publik yang sederhana. RW wajib dipilih dari `RW 01` sampai `RW 26`; slug, koordinat mentah, status, active flag, dan featured flag hanya menjadi urusan admin/sistem. Aplikasi tidak menyimpan jumlah kunjungan profil atau menyediakan sort popularitas berbasis tracking.

Upload gambar di lokal memakai public disk Laravel dan storage link `public/storage`. Di production Railway, upload diarahkan ke Cloudinary melalui custom filesystem disk karena filesystem Railway bersifat ephemeral. Gambar yang didukung pada tahap ini adalah JPG, PNG, dan WEBP dengan batas konservatif 2 MB.

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
