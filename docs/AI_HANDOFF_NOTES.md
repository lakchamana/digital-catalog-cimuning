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

## Next Steps

1. Polish detail UMKM dengan sticky CTA mobile, section Maps yang lebih jelas, dan galeri produk.
2. Mulai setup auth Laravel dan dashboard dasar.
3. Tambahkan CRUD kategori setelah auth/dashboard siap.
4. Tambahkan policy/middleware role untuk admin dan UMKM owner.
5. Tambahkan upload gambar aman untuk logo, cover, dan produk.
