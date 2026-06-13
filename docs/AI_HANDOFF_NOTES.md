# AI Handoff Notes

## Context Singkat

Project ini adalah Cimuning UMKM Online Directory, sebuah katalog online UMKM Cimuning, Kota Bekasi. Fokusnya adalah discovery, search, profil UMKM, produk/jasa, status verified, dan kontak langsung lewat WhatsApp/maps/media sosial.

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
- Livewire disiapkan sebagai dependency untuk fase search/filter/form, namun komponen Livewire database belum dibuat pada tahap pertama.
- Core Eloquent models sudah dibuat: `Category`, `Umkm`, `Product`, `ProductImage`, `UmkmContact`, dan `UmkmSocialLink`.
- Homepage, listing UMKM, listing produk, kategori, dan detail UMKM sudah membaca database jika tabel tersedia.
- View publik tetap memiliki fallback aman ketika database belum dimigrasi atau MySQL belum aktif.

## Keputusan Desain

- Sumber utama desain: `DESIGN-CIMUNING-UMKM-DIRECTORY.md`.
- Merah Cimuning digunakan hanya untuk CTA penting dan identitas.
- Background utama hangat dan bersih.
- Hijau untuk status verified/aktif.
- Biru untuk link/maps/action sekunder.
- Tombol WhatsApp menggunakan hijau WhatsApp.
- Layout dibuat mobile-first.

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
- MySQL lokal belum dimigrasi karena service di `127.0.0.1:3306` menolak koneksi. Migration dan seeder sudah lolos verifikasi dengan SQLite in-memory.

## Next Steps

1. Jalankan MySQL lokal, buat database `cimuning_umkm`, lalu jalankan `php artisan migrate --seed`.
2. Buat Livewire component untuk UMKM search dengan query string, filter kategori/RW/verified/layanan, pagination, loading state, dan empty state.
3. Tambahkan pagination pada listing `/umkm` dan `/produk`.
4. Polish detail UMKM dengan sticky CTA mobile dan galeri produk.
5. Mulai setup auth dan dashboard dasar setelah public discovery stabil.
