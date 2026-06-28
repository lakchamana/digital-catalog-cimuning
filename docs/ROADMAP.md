# Roadmap

## MVP Phase

- Setup Laravel, Tailwind, Livewire, dan Alpine.js.
- Dokumentasi awal project.
- Homepage mobile-first.
- Migration dan model inti: users, categories, umkms, products, product_images, contacts, social links.
- Listing UMKM dengan Livewire search/filter/pagination.
- Detail UMKM dengan CTA WhatsApp dan maps.
- Detail produk/jasa publik dengan galeri, deskripsi, harga opsional, UMKM pemilik, dan CTA WhatsApp.
- Auth dan dashboard dasar.
- CRUD kategori, UMKM, produk/jasa.
- Upload foto aman.
- Verifikasi UMKM.

## Phase 2

- Dashboard statistik sederhana.
- Filter lanjutan berdasarkan RW dan layanan.
- Pendaftaran UMKM lebih lengkap dengan status revisi.
- Notifikasi dashboard untuk status verifikasi.
- Account-first onboarding untuk owner UMKM.
- Homepage search-centric dengan carousel jumbotron dan kategori ikon.
- Halaman semua kategori `/kategori`.
- Interactive walkthrough first-visit publik.
- Polishing responsive dan accessibility.
- Deployment testing internal ke Railway dengan Railway MySQL dan Cloudinary upload storage.
- Dokumentasi deployment Railway/Cloudinary dan runtime caveats.
- QR profile UMKM dengan SVG download langsung ke profil publik.
- Polish onboarding owner dengan RW 01-26, copy publik, dan penghapusan field analytics lama.
- Workflow verifikasi profesional dengan submission draft, review admin read-only, audit keputusan, dan moderasi produk.
- Peninjauan ulang produk terblokir, antrean admin, notifikasi dua arah, dan log moderasi read-only.
- Administrasi akun owner terkontrol, suspend/session revocation, anonimisasi data, blokir publikasi UMKM, dan log administrasi read-only.
- Audit keamanan admin untuk autentikasi, denied access, profile changes, dan CRUD kategori dengan sanitasi data sensitif.
- Dashboard owner berbasis tindakan dengan status profil, ringkasan katalog milik sendiri, produk publik, pekerjaan tertunda, dan akses cepat keamanan akun.

## Phase 3

- Template poster/stiker QR profil UMKM untuk kebutuhan cetak/offline sharing.
- Artikel atau cerita UMKM.
- Export data UMKM untuk admin.
- Optimasi SEO lanjutan halaman detail.
- PWA ringan untuk pengalaman mobile.
- Hardening production deployment: backup database, domain custom, observability/log monitoring, dan secret rotation.
- Aktivasi email reset password yang sudah disiapkan setelah hosting publik, domain pengirim, dan SMTP tersedia.

## Future Improvement

- Laravel Scout/Meilisearch saat jumlah data membesar.
- Multi-admin approval flow.
- Featured banner dan campaign UMKM lokal.
- Manajemen banner carousel dari dashboard admin.
- Import data UMKM dari spreadsheet dengan validasi.
- Environment staging terpisah dari production jika project mulai diuji banyak pihak.

## Fitur Yang Belum Boleh Dibuat Dulu

- Payment gateway.
- Checkout.
- Cart/keranjang.
- Ongkir otomatis.
- Chat realtime internal.
- Review/rating kompleks.
- Mobile app native.
- Multi-vendor transaction system.
- Tracking klik kontak, lokasi, atau scan QR.
- Counter kunjungan profil dan ranking popularitas berbasis tracking.
