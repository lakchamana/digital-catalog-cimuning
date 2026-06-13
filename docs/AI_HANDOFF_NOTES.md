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
- Route publik tersedia untuk `/`, `/umkm`, `/umkm/{slug}`, `/produk`, `/kategori/{slug}`, `/daftar-umkm`, `/tentang`, dan `/kontak`.
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

## Next Steps

1. Uji manual `/admin` di browser dengan `admin@cimuning.test` / `password` dan owner dummy / `password`.
2. Tambahkan form pendaftaran UMKM publik yang masuk sebagai status `pending`.
3. Tambahkan validasi slug otomatis/unik agar admin dan owner tidak perlu menulis slug manual.
4. Tambahkan notifikasi dashboard untuk status verifikasi dan kebutuhan revisi.
5. Tambahkan tracking klik WhatsApp/Maps sebagai leads pada fase berikutnya.
