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
- `docs/DEPLOYMENT_UPDATE.md`

## Keputusan Teknikal

- Stack utama: Laravel, Blade, Tailwind CSS, Livewire, Alpine.js, MySQL.
- Deployment testing internal sudah disiapkan di Railway: `https://digital-catalog-cimuning-production.up.railway.app/`.
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
- Filter `/produk` menormalisasi nilai query string yang tidak valid; kategori produk juga fallback ke kategori UMKM jika `products.category_id` kosong.
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
- Public layout memiliki skip link ke `#main-content`, visible focus ring global, dan nav aktif memakai `aria-current="page"`.
- Drawer mobile, filter bottom sheet, dan walkthrough memakai dialog semantics (`role="dialog"`, `aria-modal`, `aria-labelledby`) tanpa dependency tambahan.
- Listing Livewire `/produk` dan `/umkm` memiliki live region untuk total hasil/loading/empty state serta ID filter yang dibedakan antara desktop dan mobile.
- Public layout menerima props SEO: `description`, `canonical`, `image`, `type`, dan `structuredData`.
- Detail UMKM public merender canonical, Open Graph, Twitter card, dan JSON-LD `LocalBusiness` dari data UMKM verified.
- Sitemap dinamis tersedia di `/sitemap.xml`; hanya memuat homepage, listing public, kategori aktif, dan UMKM aktif + verified.
- `robots.txt` menolak `/admin` dan mereferensikan sitemap; route `/leads/...` tidak dimasukkan ke sitemap.
- Registrasi owner `/admin/register` memakai CAPTCHA matematika lokal berbasis session dan honeypot tersembunyi, tanpa layanan eksternal.
- CAPTCHA owner registration memakai token per form render yang disimpan di session agar beberapa tab register tidak saling membatalkan jawaban.
- Form UMKM Filament sekarang memakai wizard bertahap agar owner awam tidak melihat seluruh field teknis sekaligus.
- Field slug disembunyikan dari owner untuk UMKM dan produk; sistem tetap membuat slug unik dari nama, sedangkan admin masih bisa mengedit slug sebagai field advanced.
- Helper owner berada di `App\Support\OwnerFormHelper` untuk normalisasi Instagram/TikTok dan parsing koordinat dari teks/link Maps.
- Pengambilan lokasi owner tidak memakai Google Maps API berbayar: UI menyediakan browser Geolocation, parsing koordinat, dan tombol membuka Google Maps dari alamat.
- Production Railway memakai Dockerfile, `docker-entrypoint.sh`, dan `server.php`.
- `server.php` wajib dipertahankan untuk PHP built-in server production karena Livewire/Filament JS adalah route Laravel, bukan file statis biasa.
- Config cache dan route cache production dijalankan di `docker-entrypoint.sh` saat runtime, bukan di Docker build, karena environment variables Railway tersedia saat container berjalan.
- `AppServiceProvider` memaksa HTTPS pada production dan `bootstrap/app.php` mempercayai proxy Railway agar URL asset tidak menjadi mixed content.
- Upload production memakai custom disk `cloudinary` melalui `App\Support\CloudinaryStorage`; local development tetap bisa memakai public/local disk sesuai `.env`.
- Filament upload fields and image columns should use `App\Support\UploadDisk::name()` so local `FILESYSTEM_DISK=local` maps to the public disk while production `FILESYSTEM_DISK=cloudinary` uses Cloudinary.
- Produk memiliki gambar utama di `products.image` dan galeri tambahan melalui relasi `Product::images()` ke `product_images`; dashboard Filament sekarang bisa mengelola keduanya.
- Dependency Cloudinary memakai SDK resmi `cloudinary/cloudinary_php`, bukan package `cloudinary-labs/cloudinary-laravel`, karena kompatibilitas Laravel 13.
- `.env.example` sudah diarahkan production-ready dengan variable Cloudinary, MySQL Railway, `SESSION_DRIVER=database`, dan `CACHE_STORE=database`.
- QR profil UMKM memakai package `endroid/qr-code` dan dirender sebagai SVG agar tidak bergantung pada extension GD/Imagick.
- Route QR public: `/qr/umkm/{umkm:slug}.svg` untuk SVG dan `/qr/umkm/{umkm:slug}/open` untuk tracking scan lalu redirect ke profil.
- QR hanya tersedia untuk UMKM `is_active = true` dan `status = verified`.
- Scan QR dicatat sebagai `lead_events.type = qr_scan` dengan `source = qr_profile`, lalu diarahkan ke `route('umkm.show', $slug)`.
- Pencatatan lead dipusatkan di `App\Support\LeadEventRecorder` dan dipakai oleh redirect WhatsApp/Maps serta QR scan.
- Halaman `/tentang` dan `/kontak` sudah memakai view khusus, bukan placeholder MVP.
- Kontak v1 tidak menampilkan nomor/email dummy dan tidak menyimpan pesan ke database; halaman mengarahkan user ke pencarian, direktori, daftar owner, dan login owner.

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
- Jangan menjalankan `php artisan config:cache` di Dockerfile build phase.
- Jangan menghapus `server.php` selama production masih memakai PHP built-in server/router ini.
- Jangan mengganti production upload ke `public`/`local` disk di Railway karena file upload akan hilang saat redeploy.
- Jangan mengubah QR menjadi langsung WhatsApp untuk v1; target default adalah profil UMKM agar katalog, lokasi, dan kontak tetap terlihat.

## Status Pekerjaan Terakhir

- Laravel scaffold dibuat di root project.
- Dokumentasi awal dibuat di folder `docs/`.
- Public layout, navbar, footer, button components, badges, UMKM card, homepage, dan placeholder pages dibuat.
- Placeholder `/tentang` dan `/kontak` sudah diganti dengan halaman informasi publik yang lengkap.
- Route publik tersedia untuk `/`, `/umkm`, `/umkm/{slug}`, `/produk`, `/kategori`, `/kategori/{slug}`, `/daftar-umkm`, `/tentang`, dan `/kontak`.
- Migration inti, relationship model, dan seeder dummy sudah dibuat.
- Seeder membuat admin `admin@cimuning.test` dan owner dummy dengan password `password`.
- User sudah mengaktifkan XAMPP/MySQL/Apache dan menjalankan `php artisan migrate --seed`.
- Livewire UMKM search/filter sudah dibuat dengan keyword, kategori, RW, verified, layanan, sort, pagination, loading skeleton, empty state, dan mobile bottom sheet.
- Branding sudah diganti menjadi Cimuning Digital Hub di UI utama, metadata, `.env`, dan `.env.example`.
- Livewire produk search/filter sudah dibuat dengan keyword, kategori, UMKM, harga, sort, pagination, loading skeleton, empty state, dan mobile bottom sheet.
- Livewire produk search/filter sudah diperkuat: query invalid kembali ke default, filter kategori memakai fallback kategori UMKM, harga `0` dianggap "Hubungi UMKM", dan test khusus `ProductSearchTest` menjaga perilaku ini.
- Filament v5.6.7 sudah terpasang dan panel `/admin` sudah dibuat.
- Resource admin tersedia untuk kategori, UMKM, dan produk.
- Admin bisa melakukan verifikasi UMKM melalui action cepat di tabel UMKM.
- Owner UMKM bisa masuk panel dan hanya melihat/mengelola data miliknya sendiri.
- Owner registration kini memiliki CAPTCHA lokal dan honeypot; jika gagal, pesan validasi dibuat ramah.
- Owner registration kini memakai CAPTCHA tokenized dan honeypot yang lebih tahan autofill untuk mengurangi kegagalan palsu saat jawaban hitungan benar.
- Upload logo/cover UMKM dan gambar produk memakai public disk melalui `public/storage` di lokal, dan Cloudinary saat `FILESYSTEM_DISK=cloudinary` di production.
- Upload logo/cover UMKM, gambar utama produk, dan galeri produk kini memakai helper disk upload sehingga mengikuti `FILESYSTEM_DISK` tanpa hardcode `public` di form Filament.
- Galeri foto produk sudah bisa dikelola dari Filament Product form dengan maksimal 6 gambar JPG/PNG/WEBP masing-masing 2 MB.
- Product card publik memakai prioritas gambar utama, lalu gambar galeri pertama, lalu fallback visual, serta menampilkan badge tambahan foto jika galeri berisi lebih dari satu gambar.
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
- Accessibility polish publik sudah diterapkan untuk skip link, focus ring, aria-current, drawer/filter/walkthrough dialog attributes, live region hasil pencarian, dan duplikasi ID filter.
- Test aksesibilitas publik sudah ditambahkan untuk layout landmarks, filter drawer, live region, dan render route publik utama.
- SEO public tahap awal sudah ditambahkan untuk detail UMKM dan sitemap publik.
- Test SEO public sudah ditambahkan untuk meta detail UMKM, fallback social image, sitemap, dan robots.
- Owner onboarding sudah dipoles: form UMKM owner berupa wizard, slug tidak perlu diisi owner, koordinat bisa dibantu dari Geolocation atau teks Maps, dan social media boleh berupa username atau URL.
- Test owner onboarding sudah diperluas untuk CAPTCHA, honeypot, slug otomatis, koordinat Maps, dan normalisasi Instagram/TikTok.
- Deployment Railway + Cloudinary sudah ditambahkan oleh AI agent lain dan didokumentasikan di `docs/DEPLOYMENT_UPDATE.md`.
- File deployment yang sudah ada: `Dockerfile`, `docker-entrypoint.sh`, `server.php`, `config/cloudinary.php`, dan `App\Support\CloudinaryStorage`.
- Railway MySQL digunakan untuk production testing; migration dan seeder dijalankan otomatis saat container start dengan `--force`.
- `nixpacks.toml` disebut di deployment update sebagai alternatif lama, tetapi file tersebut tidak ada di workspace saat catatan ini diperbarui; Railway memprioritaskan Dockerfile.
- QR profil UMKM sudah ditambahkan untuk UMKM verified + active, termasuk public QR card di detail UMKM dan action download QR di Filament.
- Lead analytics sudah mengenali `qr_scan` sebagai “Scan QR”.
- Test QR profil sudah ditambahkan untuk SVG, tracking redirect, proteksi UMKM non-public, dan scoping owner lead.
- Halaman Tentang dan Kontak/Bantuan publik sudah dilengkapi dengan SEO metadata, CTA, footer links, dan copy tanpa kontak palsu.
- Test halaman informasi publik sudah ditambahkan untuk mencegah regresi ke placeholder.

## Next Steps

1. Uji manual `/admin/register` di browser asli dengan autofill aktif untuk memastikan CAPTCHA tokenized tidak gagal palsu.
2. Uji manual filter `/produk` untuk kategori produk, fallback kategori UMKM, harga, sort, pagination, dan reset query.
3. Uji manual `/admin` di browser dengan `admin@cimuning.test` / `password` dan owner dummy / `password`.
4. Uji manual homepage mobile/desktop untuk memastikan carousel terasa natural, tombol rapi, dan kategori tidak terlalu padat.
5. Lanjutkan validasi copy setelah uji manual mobile.
6. Pertimbangkan template poster/stiker QR setelah kebutuhan desain cetak final tersedia.
7. Uji manual upload gambar di Railway/Cloudinary untuk memastikan URL yang tersimpan tampil di public card dan tabel Filament.
8. Pertimbangkan email notification dan password reset flow setelah konfigurasi mail siap.
9. Tambahkan export data lead/UMKM untuk admin bila sudah dibutuhkan operasional.
10. Uji manual wizard UMKM owner di perangkat mobile, terutama tombol "Gunakan lokasi saya" dan "Buka Google Maps".
11. Pertimbangkan dashboard tutorial khusus owner jika wizard Filament masih terasa membingungkan untuk pengguna awam.
12. Uji manual production Railway setelah setiap perubahan besar: homepage, `/produk`, `/umkm`, `/admin`, Livewire JS, Filament asset, upload Cloudinary, dan mixed-content console.
