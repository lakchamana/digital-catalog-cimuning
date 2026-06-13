# Project Context

## Ringkasan

Cimuning UMKM Online Directory adalah web app katalog online untuk membantu masyarakat menemukan UMKM lokal di Cimuning, Kota Bekasi. Platform ini berfungsi sebagai etalase digital dan direktori usaha, bukan e-commerce checkout.

## Jenis Platform

Platform ini adalah online directory/katalog digital. Fokus utama aplikasi adalah pencarian UMKM, profil usaha, produk/jasa, status verifikasi, dan jalur kontak langsung seperti WhatsApp, telepon, maps, website, dan media sosial.

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
- Auth: Laravel authentication system pada fase dashboard.

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

## Fitur Ditunda

- Payment gateway.
- Checkout.
- Cart/keranjang.
- Ongkir otomatis.
- Chat realtime internal.
- Review/rating kompleks.
- Mobile app native.
- Multi-vendor transaction system.

## Catatan Untuk AI Berikutnya

Jangan mengubah arah platform menjadi e-commerce. Prioritaskan search, direktori, profil UMKM, mobile-first UI, keamanan role, validasi form, dan kontak langsung ke UMKM. Update `docs/CHANGELOG.md` dan `docs/AI_HANDOFF_NOTES.md` setiap selesai task besar.
