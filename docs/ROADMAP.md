# Roadmap

## MVP Phase

- Setup Laravel, Tailwind, Livewire, dan Alpine.js.
- Dokumentasi awal project.
- Homepage mobile-first.
- Migration dan model inti: users, categories, umkms, products, product_images, contacts, social links.
- Listing UMKM dengan Livewire search/filter/pagination.
- Detail UMKM dengan CTA WhatsApp dan maps.
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

## Phase 3

- Template poster/stiker QR profil UMKM untuk kebutuhan cetak/offline sharing.
- Artikel atau cerita UMKM.
- Export data UMKM untuk admin.
- Optimasi SEO halaman detail.
- Tutorial/dashboard guidance khusus owner bila dibutuhkan.
- PWA ringan untuk pengalaman mobile.
- Hardening production deployment: backup database, domain custom, observability/log monitoring, dan secret rotation.
- Email operasional dan password reset setelah domain serta konfigurasi mail tersedia.

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
