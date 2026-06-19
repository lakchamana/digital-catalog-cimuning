Kamu adalah AI software engineer senior yang membantu saya membangun web app **Cimuning UMKM Online Directory** menggunakan Laravel.

Saya ingin kamu membangun project ini dengan arah yang jelas, rapi, aman, mobile-friendly, dan mudah dilanjutkan oleh AI/model lain di masa depan.

## 1. Konteks Project

Project ini adalah platform **Online Directory / Katalog Online UMKM Cimuning, Kota Bekasi**.

Website ini **bukan e-commerce checkout**. Tidak ada payment gateway, cart, checkout, ongkir otomatis, atau transaksi uang di dalam website.

Fokus utama platform:

* Menampilkan profil UMKM lokal.
* Menampilkan produk/jasa UMKM.
* Membantu masyarakat mencari UMKM berdasarkan keyword, kategori, lokasi/RW, layanan, dan status verified.
* Menghubungkan calon pembeli dengan UMKM melalui WhatsApp, telepon, maps, dan media sosial.
* Membantu admin mengelola dan memverifikasi data UMKM.

Flow utama user:

1. User membuka homepage.
2. User mencari produk, jasa, atau nama UMKM lewat search bar.
3. User melihat hasil pencarian/listing UMKM.
4. User membuka detail UMKM.
5. User menghubungi UMKM melalui WhatsApp/maps/sosial media.
6. Transaksi dilakukan langsung antara user dan UMKM di luar website.

## 2. Stack yang Digunakan

Gunakan stack berikut:

* Backend: Laravel
* Database: MySQL
* Frontend: Blade + Tailwind CSS
* Interactivity: Livewire + Alpine.js
* Search/filter/pagination: Livewire
* UI kecil seperti dropdown, modal, drawer, hamburger menu: Alpine.js
* Auth: Laravel authentication system
* Build style: Mobile-first, responsive, clean, dan accessible

Jangan gunakan React/Vue dulu kecuali benar-benar dibutuhkan. Project ini harus tetap dekat dengan ekosistem Laravel agar mudah saya pelajari dan mudah dikembangkan dengan Codex.

## 3. File Referensi Desain

Di dalam project, baca dan ikuti file desain berikut jika tersedia:

* `DESIGN-CIMUNING-UMKM-DIRECTORY.md`
* `www.bridestory.com-DESIGN.md`
* `www.indonetwork.co.id-DESIGN.md`

Prioritas desain:

1. Ikuti `DESIGN-CIMUNING-UMKM-DIRECTORY.md` sebagai sumber utama.
2. Ambil struktur direktori/katalog dari referensi Indonetwork.
3. Ambil rasa visual card, whitespace, dan vendor discovery dari referensi Bridestory.
4. Jangan menyalin mentah desain referensi, tetapi adaptasikan untuk UMKM Cimuning.

Arah visual utama:

* Local Directory
* Clean Marketplace
* Community Trust
* Mobile Friendly
* Ramah untuk masyarakat umum dan pelaku UMKM Indonesia

## 4. Prinsip Desain Utama

Gunakan filosofi warna:

“Merah Semangat, Putih Terbuka, Hijau Tumbuh, Biru Terpercaya.”

Palet warna utama:

* Cimuning Red: `#E60012`
* Trust Red: `#9F111B`
* Community Blush: `#FFE8E8`
* Rukun White: `#FFFDF9`
* Pure White: `#FFFFFF`
* Charcoal: `#1F2933`
* Slate Gray: `#667085`
* Soft Gray: `#98A2B3`
* Light Border: `#E5E7EB`
* Warm Section: `#F9FAFB`
* Growth Green: `#16A34A`
* Connect Blue: `#2563EB`
* WhatsApp Green: `#25D366`

Aturan penting:

* Logo merah adalah identitas utama, tetapi jangan membuat semua halaman dominan merah.
* Gunakan merah hanya untuk CTA penting seperti “Daftarkan UMKM” atau “Cari UMKM”.
* Gunakan hijau untuk status verified/aktif.
* Gunakan biru untuk link/maps/action sekunder.
* Gunakan hijau WhatsApp hanya untuk tombol WhatsApp.
* Background utama harus bersih, hangat, dan nyaman dilihat.

## 5. Mobile-First Requirement

Ini catatan paling penting:

Mayoritas user kemungkinan memakai smartphone, jadi seluruh web app harus **sangat mobile friendly**.

Pastikan:

* Semua halaman nyaman digunakan di layar HP.
* Search bar mudah ditemukan dan mudah digunakan.
* Filter di mobile menggunakan drawer/bottom sheet.
* Card UMKM mudah dibaca di layar kecil.
* Tombol WhatsApp terlihat jelas.
* Detail UMKM memiliki CTA yang mudah dijangkau.
* Button minimal tinggi `44px`.
* Text body minimal `16px`.
* Jangan membuat table lebar di mobile; ubah menjadi card/list.
* Header mobile harus ringan dan tidak memakan banyak ruang.
* Gunakan layout 1 kolom di mobile, 2 kolom tablet, 3–4 kolom desktop.
* Pastikan desain responsive sebelum membuat fitur tambahan.

## 6. Fitur MVP yang Harus Dibangun

Bangun MVP terlebih dahulu. Jangan membuat fitur terlalu besar di awal.

### Public Pages

Buat halaman:

* `/` — Homepage
* `/umkm` — Listing UMKM dengan search dan filter
* `/umkm/{slug}` — Detail UMKM
* `/produk` — Listing produk/jasa
* `/kategori/{slug}` — Listing berdasarkan kategori
* `/daftar-umkm` — Landing/form pendaftaran UMKM
* `/tentang` — Tentang platform
* `/kontak` — Kontak pengelola

### Dashboard Pages

Buat halaman dashboard:

* `/dashboard`
* `/dashboard/umkm`
* `/dashboard/products`
* `/dashboard/categories`
* `/dashboard/verification`
* `/dashboard/profile`

### Role

Buat minimal 3 role:

* Admin
* UMKM Owner
* Public User / Guest

### Admin dapat:

* Login ke dashboard.
* Mengelola kategori.
* Mengelola data UMKM.
* Verifikasi UMKM.
* Mengelola produk/jasa.
* Mengelola foto.
* Mengubah status pending/verified/rejected/need revision.

### UMKM Owner dapat:

* Login.
* Mengelola profil UMKM miliknya sendiri.
* Menambah/mengedit/menghapus produk/jasa miliknya sendiri.
* Upload foto.
* Melihat status verifikasi.
* Tidak boleh mengedit data UMKM lain.

### Public user dapat:

* Melihat homepage.
* Mencari UMKM.
* Melihat produk/jasa.
* Melihat detail UMKM.
* Menghubungi UMKM via WhatsApp/maps/sosial media.
* Tidak wajib login.

## 7. Fitur yang Jangan Dibuat Dulu

Jangan buat fitur berikut di MVP:

* Payment gateway
* Checkout
* Cart/keranjang
* Ongkir otomatis
* Chat realtime internal
* Review/rating kompleks
* Mobile app native
* Multi-vendor transaction system

Kalau nanti dibutuhkan, masukkan ke roadmap, bukan MVP.

## 8. Search dan Filter

Search adalah fitur inti platform.

Gunakan Livewire untuk:

* Keyword search
* Filter kategori
* Filter lokasi/RW
* Filter status verified
* Filter layanan
* Sort
* Pagination
* Loading state
* Empty state

Search harus bisa mencari dari:

* Nama UMKM
* Nama produk/jasa
* Kategori
* Deskripsi
* Tag
* Lokasi/RW

Filter awal:

* Kategori
* Lokasi/RW
* Verified
* Layanan: delivery, COD, custom order, toko fisik
* Sort: terbaru, A-Z, populer

Gunakan debounce sekitar 400–600ms.

Pastikan query search bisa muncul di URL agar hasil pencarian bisa dibagikan.

## 9. Struktur Database Awal

Buat migration/model/controller sesuai kebutuhan.

Minimal tabel:

* `users`
* `roles` atau role column sederhana
* `categories`
* `umkms`
* `products`
* `product_images`
* `umkm_social_links`
* `umkm_contacts`
* `banners` optional
* Tidak menyimpan tracking klik kontak, scan QR, IP, user agent, atau referer pengunjung

Relasi utama:

* User memiliki satu UMKM.
* UMKM memiliki banyak produk.
* UMKM memiliki satu kategori utama.
* Produk memiliki banyak gambar.
* UMKM memiliki banyak social links/contact links.

Field penting UMKM:

* user_id
* category_id
* name
* slug
* description
* owner_name
* phone
* whatsapp
* email
* address
* rw
* latitude
* longitude
* instagram
* tiktok
* website
* cover_image
* logo_image
* status: pending, verified, rejected, need_revision
* is_featured
* is_active
* service_delivery
* service_cod
* service_custom_order
* has_physical_store

Field penting product:

* umkm_id
* category_id
* name
* slug
* description
* price nullable
* image
* is_active

## 10. Security Requirement

Terapkan security sejak awal:

* Gunakan CSRF protection di semua form.
* Gunakan validation request untuk semua input.
* Batasi akses berdasarkan role.
* UMKM owner hanya boleh mengubah data miliknya sendiri.
* Admin saja yang boleh verifikasi.
* Upload gambar hanya boleh JPG, PNG, WEBP.
* Batasi ukuran upload.
* Rename file upload agar aman.
* Jangan render input user secara mentah.
* Hindari raw SQL. Gunakan Eloquent/Query Builder.
* Tambahkan rate limit untuk login dan form penting jika memungkinkan.
* Gunakan confirmation modal untuk delete/reject.
* Jangan tampilkan error teknis ke public user.

## 11. UI Component yang Harus Dibuat

Buat reusable Blade components:

* Public layout
* Dashboard layout
* Navbar
* Mobile navigation drawer
* Footer
* UMKM card
* Product card
* Category badge
* Verified badge
* Service badge
* Empty state
* Primary button
* Secondary button
* WhatsApp button
* Search input
* Filter drawer
* Modal confirmation

Buat Livewire components:

* Public UMKM search
* Product search
* Category filter
* UMKM form
* Product form
* Image uploader

## 12. UX Copy

Gunakan bahasa Indonesia yang ramah dan mudah dipahami.

Contoh copy:

Hero:
“Temukan UMKM Cimuning dengan lebih mudah”

Subhero:
“Cari makanan, jasa, toko harian, produk kreatif, dan usaha lokal di sekitar Cimuning.”

Placeholder search:
“Cari produk, jasa, atau nama UMKM...”

CTA:
“Cari UMKM”
“Daftarkan UMKM”
“Lihat Detail”
“Chat WhatsApp”
“Lihat Lokasi”

Trust note:
“Platform ini membantu mempertemukan pembeli dan pelaku UMKM. Transaksi dilakukan langsung dengan pemilik usaha.”

Empty state:
“UMKM belum ditemukan. Coba gunakan kata kunci lain atau pilih kategori berbeda.”

## 13. Dokumentasi Project yang Harus Dibuat

Buat folder dokumentasi:

`docs/`

Lalu buat file berikut:

### `docs/PROJECT_CONTEXT.md`

Isi file ini dengan:

* Ringkasan tujuan project.
* Jenis platform.
* Target user.
* Alasan tidak ada payment gateway.
* Stack yang digunakan.
* Prinsip desain.
* Prinsip mobile-first.
* Role user.
* Fitur MVP.
* Fitur yang ditunda.
* Catatan penting untuk AI/model berikutnya.

File ini harus cukup jelas sehingga jika saya berganti model AI, model tersebut bisa memahami konteks project hanya dari membaca file ini.

### `docs/WEB_APP_FLOW.md`

Isi file ini dengan:

* Flow public user.
* Flow UMKM owner.
* Flow admin.
* Flow pencarian UMKM.
* Flow pendaftaran UMKM.
* Flow verifikasi UMKM.
* Flow tambah produk/jasa.
* Flow kontak WhatsApp/maps.

### `docs/ROADMAP.md`

Isi file ini dengan:

* MVP phase.
* Phase 2.
* Phase 3.
* Future improvement.
* Fitur yang belum boleh dibuat dulu.
* Potensi pengembangan seperti analytics klik WhatsApp, QR profile UMKM, artikel UMKM, PWA, Laravel Scout/Meilisearch, dan dashboard statistik.

### `docs/CHANGELOG.md`

Isi file ini sebagai catatan perubahan project.

Gunakan format:

```md
# Changelog

## [Unreleased]

### Added
- Initial project setup.

### Changed
- 

### Fixed
- 

### Notes
- 
```

Setiap kali kamu membuat perubahan besar, update `docs/CHANGELOG.md`.

### `docs/AI_HANDOFF_NOTES.md`

Isi file ini dengan catatan untuk AI/model berikutnya:

* Context singkat project.
* File penting yang harus dibaca.
* Keputusan teknikal yang sudah diambil.
* Keputusan desain yang sudah diambil.
* Larangan penting seperti jangan membuat payment/checkout.
* Prioritas mobile-first.
* Status pekerjaan terakhir.
* Next steps.

Setiap kali selesai mengerjakan task besar, update file ini agar AI berikutnya tidak kehilangan konteks.

## 14. Cara Kerja Saat Membantu Saya

Kerjakan secara bertahap, jangan langsung membangun semua sekaligus.

Urutan kerja yang disarankan:

1. Baca file desain dan dokumentasi jika ada.
2. Buat struktur project Laravel.
3. Setup Tailwind, Livewire, Alpine.js.
4. Buat file dokumentasi di folder `docs/`.
5. Buat migration dan model inti.
6. Buat layout public mobile-first.
7. Buat homepage.
8. Buat listing UMKM dengan search Livewire.
9. Buat detail UMKM.
10. Buat auth dan dashboard.
11. Buat CRUD kategori.
12. Buat CRUD UMKM.
13. Buat CRUD produk/jasa.
14. Buat verifikasi UMKM.
15. Polish responsive/mobile.
16. Review security dan validation.
17. Update changelog dan AI handoff notes.

Setelah menyelesaikan satu tahap besar, berikan ringkasan:

* Apa yang dibuat.
* File apa yang berubah.
* Cara menjalankan/mengetes.
* Apa next step yang disarankan.

## 15. Coding Style

Gunakan Laravel convention.

Pastikan:

* Nama route rapi.
* Controller tidak terlalu gemuk.
* Gunakan Form Request untuk validasi jika memungkinkan.
* Gunakan Policy/Gate atau middleware untuk role access.
* Gunakan Blade component untuk UI berulang.
* Gunakan Livewire untuk interaksi data.
* Gunakan Alpine.js hanya untuk UI behavior ringan.
* Gunakan Tailwind utility dengan class yang konsisten.
* Gunakan slug untuk URL detail.
* Gunakan pagination untuk listing.
* Gunakan eager loading untuk menghindari N+1 query.

## 16. Acceptance Criteria

Sebuah fitur dianggap selesai jika:

* Berfungsi di desktop dan mobile.
* Tampilan mobile nyaman digunakan.
* Validasi form berjalan.
* Empty state tersedia.
* Loading state tersedia jika fitur menggunakan Livewire.
* Role access aman.
* Tidak ada flow payment/checkout.
* Dokumentasi terkait diperbarui.
* `docs/CHANGELOG.md` diperbarui.
* `docs/AI_HANDOFF_NOTES.md` diperbarui.

## 17. Tugas Pertama

Mulai dengan melakukan hal berikut:

1. Analisis struktur project saat ini.
2. Jika project belum dibuat, buat project Laravel baru.
3. Setup Tailwind CSS, Livewire, dan Alpine.js.
4. Buat folder `docs/`.
5. Buat file:

   * `docs/PROJECT_CONTEXT.md`
   * `docs/WEB_APP_FLOW.md`
   * `docs/ROADMAP.md`
   * `docs/CHANGELOG.md`
   * `docs/AI_HANDOFF_NOTES.md`
6. Isi semua file dokumentasi tersebut berdasarkan brief ini.
7. Buat layout awal public yang mobile-first:

   * Navbar responsive
   * Hero section
   * Search bar besar
   * Section kategori populer
   * Section UMKM pilihan
   * CTA daftar UMKM
   * Footer
8. Jangan buat payment, checkout, cart, atau fitur transaksi.
9. Setelah selesai, jelaskan file yang dibuat/diubah dan next step yang harus saya lakukan.
