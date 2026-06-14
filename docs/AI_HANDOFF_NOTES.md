# AI Handoff Notes

## Context Singkat

Project ini adalah Cimuning Digital Hub, sebuah katalog online UMKM Cimuning, Kota Bekasi. Fokusnya adalah discovery, search, profil UMKM, katalog produk digital, status verified, lokasi Google Maps, dan kontak langsung lewat WhatsApp/media sosial.

## File Penting Yang Harus Dibaca

- `prompt-pertama.md`
- `DESIGN-CIMUNING-UMKM-DIRECTORY.md`
- `www.indonetwork.co.id-DESIGN.md`
- `www.bridestory.com-DESIGN.md`
- `docs/PROJECT_CONTEXT.md`
- `docs/WEB_APP_FLOW.md`
- `docs/ROADMAP.md`

## Keputusan Teknikal

- Stack utama: Laravel, Blade, Tailwind CSS, Livewire, Alpine.js, MySQL.
- Tailwind v4 digunakan melalui Vite dan token warna didefinisikan di `resources/css/app.css`.
- Alpine.js dipakai untuk UI ringan seperti mobile drawer.
- Livewire digunakan untuk listing `/umkm` melalui `App\Livewire\Public\UmkmSearch`.
- Livewire digunakan untuk listing `/produk` melalui `App\Livewire\Public\ProductSearch`.
- Laravel Filament 5 digunakan untuk back office `/admin`, bukan untuk mengganti UI publik.
- Akses panel Filament dibatasi lewat `User::canAccessPanel()` untuk role `admin` dan `umkm_owner`.
- Policy kategori, UMKM, dan produk didaftarkan eksplisit di `AppServiceProvider`.
- Resource Filament melakukan scoping data: admin melihat semua, UMKM owner hanya melihat UMKM/produk miliknya.
- Core Eloquent models sudah dibuat: `Category`, `Umkm`, `Product`, `ProductImage`, `UmkmContact`, dan `UmkmSocialLink`.
- Homepage, listing UMKM, listing produk, kategori, dan detail UMKM sudah membaca database jika tabel tersedia.
- View publik tetap memiliki fallback aman ketika database belum dimigrasi atau MySQL belum aktif.
- Listing `/umkm` menyimpan filter di query string: `search`, `category`, `rw`, `verified`, `services`, `sort`, `perPage`, dan `page`.
- Listing `/produk` menyimpan filter di query string: `search`, `category`, `umkm`, `price`, `sort`, `perPage`, dan `page`.
- Pendaftaran publik `/daftar-umkm` memakai Livewire `App\Livewire\Public\UmkmRegistrationForm`.
- Slug unik dibuat lewat helper `App\Support\UniqueSlug` dan dipakai pada pendaftaran publik serta auto-fill form Filament.
- Notifikasi dashboard memakai Laravel database notifications dan Filament notification bell dengan polling 30 detik.
- Perubahan status verifikasi UMKM dipusatkan di `App\Support\UmkmVerificationWorkflow`.
- Pembuatan UMKM pending menotifikasi semua user role `admin`; action verifikasi/revisi/tolak menotifikasi owner jika UMKM memiliki `user_id`.
- Tracking klik WhatsApp/Maps memakai event detail di `lead_events` dan redirect route `/leads/{umkm:slug}/{type}`.
- Lead analytics di Filament memakai scope `LeadEvent::visibleTo($user)`: admin melihat semua, owner hanya UMKM miliknya.
- IP lead disimpan sebagai hash, bukan raw IP.
- Pendaftaran UMKM sekarang account-first: calon owner membuat akun di `/admin/register`, lalu mengisi profil UMKM dari panel.
- `/daftar-umkm` adalah landing onboarding owner, bukan form guest submission.
- Owner baru otomatis mendapat role `umkm_owner`; UMKM yang dibuat owner dipaksa `pending` dan `is_active = false`.
- Homepage diarahkan search-centric dan discovery-first: navbar memiliki search besar ke `/produk`, carousel jumbotron informatif, kategori ikon, produk/jasa aktif dari UMKM verified, lalu section UMKM pilihan.
- Carousel homepage memakai Alpine.js dengan horizontal-only `scrollTo`, bukan `scrollIntoView`, agar auto-slide tidak menarik viewport kembali ke atas saat user scroll katalog.
- Auto-advance carousel dipause saat carousel tidak terlihat di viewport melalui `IntersectionObserver`, serta pause saat hover/focus.
- Route `/kategori` adalah halaman semua kategori aktif; `/kategori/{slug}` tetap dipakai untuk listing kategori tertentu.
- Komponen `x-category-icon` memetakan slug kategori ke ikon SVG lokal dengan fallback.
- Tutorial first-visit publik memakai komponen `x-first-visit-onboarding` sebagai interactive walkthrough dengan localStorage key `cimuning_walkthrough_seen_v1`.
- Tutorial hanya untuk public layout; panel Filament belum punya tutorial custom.

## Keputusan Desain

- Sumber utama desain: `DESIGN-CIMUNING-UMKM-DIRECTORY.md`.
- Merah Cimuning digunakan hanya untuk CTA penting dan identitas.
- Background utama hangat dan bersih.
- Hijau untuk status verified/aktif.
- Biru untuk link/maps/action sekunder.
- Tombol WhatsApp menggunakan hijau WhatsApp.
- Layout dibuat mobile-first.
- Logo utama berada di `public/assets/brand/logo-cimuning.png` dan juga dipakai sebagai favicon PNG.
- Nama aplikasi utama adalah `Cimuning Digital Hub`.

## Larangan Penting

- Jangan membuat payment gateway.
- Jangan membuat checkout.
- Jangan membuat cart/keranjang.
- Jangan membuat ongkir otomatis.
- Jangan mengubah platform menjadi marketplace transaksi.
- Jangan mewajibkan login untuk public user yang hanya ingin mencari UMKM.

## Status Pekerjaan Terakhir

- Laravel scaffold dibuat di root project.
- Dokumentasi awal dibuat di folder `docs/`.
- Public layout, navbar, footer, button components, badges, UMKM card, homepage, dan placeholder pages dibuat.
- Route publik tersedia untuk `/`, `/umkm`, `/umkm/{slug}`, `/produk`, `/kategori`, `/kategori/{slug}`, `/daftar-umkm`, `/tentang`, dan `/kontak`.
- Migration inti, relationship model, dan seeder dummy sudah dibuat.
- Seeder membuat admin `admin@cimuning.test` dan owner dummy dengan password `password`.
- User sudah mengaktifkan XAMPP/MySQL/Apache dan menjalankan `php artisan migrate --seed`.
- Livewire UMKM search/filter sudah dibuat dengan keyword, kategori, RW, verified, layanan, sort, pagination, loading skeleton, empty state, dan mobile bottom sheet.
- Branding sudah diganti menjadi Cimuning Digital Hub di UI utama, metadata, `.env`, dan `.env.example`.
- Livewire produk search/filter sudah dibuat dengan keyword, kategori, UMKM, harga, sort, pagination, loading skeleton, empty state, dan mobile bottom sheet.
- Filament v5.6.7 sudah terpasang dan panel `/admin` sudah dibuat.
- Resource admin tersedia untuk kategori, UMKM, dan produk.
- Admin bisa melakukan verifikasi UMKM melalui action cepat di tabel UMKM.
- Owner UMKM bisa masuk panel dan hanya melihat/mengelola data miliknya sendiri.
- Upload logo/cover UMKM dan gambar produk memakai public disk melalui `public/storage`.
- `php artisan test` sudah hijau dan berisi test tambahan untuk akses panel dan scoping owner.
- Halaman detail UMKM `/umkm/{slug}` sudah dipoles dengan hero gambar, logo UMKM, badge layanan, Maps embed/link, sticky contact panel desktop, sticky CTA mobile, dan katalog produk berbasis gambar upload.
- Route detail UMKM sudah eager-load `products.images` untuk menghindari N+1 pada galeri produk.
- Placeholder `/daftar-umkm` sempat diganti dengan form pendaftaran publik, lalu diganti lagi menjadi account-first onboarding landing.
- Form guest Livewire lama masih ada di kode tetapi tidak dirender publik pada flow terbaru.
- Owner mengisi logo/cover UMKM dari Filament public disk dengan validasi JPG/PNG/WEBP maksimal 2 MB.
- Filament form kategori, UMKM, dan produk sudah auto-fill slug dari nama.
- Tabel UMKM Filament memiliki action admin: Verifikasi, Minta revisi, dan Tolak.
- Test pendaftaran publik sudah ditambahkan, termasuk slug unik, upload invalid, dan proteksi UMKM pending dari public listing/detail.
- Migration `notifications` sudah ditambahkan untuk database notifications.
- Filament notification bell sudah aktif di panel `/admin`.
- Dashboard admin memiliki widget `UMKM perlu ditinjau` untuk status `pending` dan `need_revision`.
- Dashboard owner memiliki widget status UMKM miliknya.
- Test notifikasi dashboard sudah ditambahkan untuk pendaftaran publik dan action verifikasi/revisi/tolak.
- Tracking klik WhatsApp dan Maps sudah ditambahkan untuk detail UMKM, sticky CTA mobile, Maps section, product card, UMKM listing, dan UMKM pilihan homepage.
- Dashboard Filament memiliki widget statistik lead dan aktivitas lead terbaru.
- Test lead tracking sudah ditambahkan untuk redirect, target kosong, UMKM non-public, relasi produk, dan scoping owner.
- Filament registration sudah aktif di `/admin/register` dengan custom page owner registration.
- Setelah register, owner diarahkan ke halaman create UMKM.
- Homepage sudah dirombak menjadi search-centric dengan navbar search besar, carousel jumbotron ala OLX, kategori ikon termasuk "Lihat Semua", produk terbaru, dan UMKM pilihan.
- Carousel homepage sudah dipoles: tombol prev/next floating lebih rapi, dots tetap sinkron, swipe mobile tetap didukung, dan auto-slide tidak mengganggu section katalog bawah.
- Halaman `/kategori` sudah tersedia untuk melihat semua kategori aktif dalam grid ikon, deskripsi, dan jumlah UMKM verified.
- Seeder kategori sudah ditambah dengan Pendidikan, Kesehatan, Laundry, Elektronik, Agribisnis, Properti/Rumah, Event & Catering, dan Anak & Bayi.
- Tutorial first-visit publik sudah diganti menjadi interactive walkthrough bertahap untuk search, kategori/produk, dan daftar akun owner.
- Test discovery publik sudah ditambahkan untuk navbar search, carousel, category shortcuts, `/kategori`, dan walkthrough.

## Next Steps

1. Uji manual `/admin` di browser dengan `admin@cimuning.test` / `password` dan owner dummy / `password`.
2. Uji manual homepage mobile/desktop untuk memastikan carousel terasa natural, tombol rapi, dan kategori tidak terlalu padat.
3. Poles accessibility form dan validasi copy setelah uji manual mobile.
4. Pertimbangkan email notification dan password reset flow setelah konfigurasi mail siap.
5. Tambahkan export data lead/UMKM untuk admin bila sudah dibutuhkan operasional.
6. Tambahkan tutorial/dashboard guidance khusus owner jika onboarding Filament dirasa masih membingungkan.
