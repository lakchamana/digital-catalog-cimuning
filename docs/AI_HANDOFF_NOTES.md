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
- Homepage saat ini memakai data dummy statis.

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
- Route publik awal tersedia untuk `/`, `/umkm`, `/produk`, `/daftar-umkm`, `/tentang`, dan `/kontak`.

## Next Steps

1. Pastikan dependency Composer dan npm terinstall.
2. Buat migration dan model inti untuk categories, umkms, products, images, contacts, dan social links.
3. Buat seeder dummy agar homepage dan listing tidak lagi memakai array statis.
4. Buat Livewire component untuk UMKM search dengan query string, filter, pagination, loading state, dan empty state.
5. Bangun detail UMKM dengan CTA WhatsApp/maps dan sticky mobile CTA.
