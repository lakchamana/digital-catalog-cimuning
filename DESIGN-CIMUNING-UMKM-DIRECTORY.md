# DESIGN.md — Cimuning UMKM Online Directory

> Dokumen ini menjadi acuan desain awal untuk membangun web app **Cimuning UMKM Online Directory** menggunakan **Laravel + Blade + Livewire + Alpine.js + Tailwind CSS + MySQL**.  
> Platform ini bukan e-commerce dengan checkout/payment gateway, melainkan **direktori digital / katalog online UMKM** yang mempertemukan pelaku usaha lokal dengan calon pembeli melalui profil, katalog produk/jasa, tombol WhatsApp, maps, dan informasi kontak.

---

## 1. Product Direction

### Nama Konsep
**Cimuning UMKM Online Directory**

### Jenis Platform
Online directory / katalog digital UMKM lokal.

### Tujuan Utama
Membantu masyarakat menemukan UMKM di wilayah Cimuning, Kota Bekasi, serta membantu pelaku UMKM mempromosikan produk/jasa mereka secara digital tanpa sistem pembayaran di dalam website.

### Prinsip Produk
- Website bertindak sebagai **etalase digital**, bukan marketplace transaksi.
- Tidak ada payment gateway, checkout, keranjang belanja, atau ongkir otomatis pada fase awal.
- Transaksi diarahkan ke luar website melalui WhatsApp, telepon, media sosial, atau kunjungan langsung.
- Fokus utama adalah **pencarian, kepercayaan, kemudahan kontak, dan promosi UMKM lokal**.

### Primary User
1. **Masyarakat umum** yang ingin mencari produk/jasa lokal.
2. **Pelaku UMKM Cimuning** yang ingin menampilkan usaha dan produk/jasanya.
3. **Admin kelurahan/komunitas/pengelola** yang memverifikasi dan mengelola data UMKM.

---

## 2. Design Philosophy

### Core Design Statement
Desain harus terasa **lokal, terpercaya, ramah, bersih, dan mudah digunakan oleh semua kalangan**.

### Visual Personality
- Community-first
- Clean marketplace
- Local trust
- Accessible and friendly
- Not too corporate, not too playful
- Modern but still familiar for masyarakat umum

### Design Inspiration
Arah desain menggabungkan:
- Struktur marketplace/direktori yang jelas, seperti katalog B2B: search besar, kategori, filter, grid produk, dan detail vendor.
- Rasa visual vendor discovery yang lebih premium: foto besar, whitespace lega, card bersih, dan profil usaha yang terlihat rapi.

### Experience Goal
User harus bisa memahami website dalam beberapa detik:

1. Apa ini?
2. Cari apa?
3. UMKM mana yang cocok?
4. Bagaimana cara menghubungi penjual?

---

## 3. Logo Analysis & Brand Meaning

Logo menggunakan bentuk **perisai merah** dengan huruf **C putih**.

### Elemen Logo
| Elemen | Makna |
|---|---|
| Perisai | Perlindungan, kepercayaan, keamanan data UMKM |
| Merah | Semangat, keberanian, energi masyarakat lokal |
| Huruf C | Cimuning, community, connect, catalog |
| Putih | Keterbukaan, kejujuran, netralitas |
| Bentuk sederhana | Mudah diterapkan di website, favicon, mobile, banner, dan media sosial |

### Brand Principle
Logo merah harus menjadi identitas utama, tetapi **warna merah tidak boleh mendominasi seluruh halaman** agar tampilan tidak terasa panas, agresif, atau melelahkan mata.

Merah digunakan sebagai **brand accent**, bukan sebagai background besar di semua area.

---

## 4. Color Palette

### Core Palette

| Role | Nama Warna | Hex | Penggunaan |
|---|---|---:|---|
| Primary Brand | Cimuning Red | `#E60012` | Logo, CTA utama, highlight penting |
| Deep Brand | Trust Red | `#9F111B` | Hover CTA, aksen kuat, footer accent |
| Soft Brand | Community Blush | `#FFE8E8` | Badge, background lembut, alert ringan |
| Main Background | Rukun White | `#FFFDF9` | Background utama agar tidak terlalu silau |
| Surface | Pure White | `#FFFFFF` | Card, modal, form, navbar |
| Primary Text | Charcoal | `#1F2933` | Judul, teks utama |
| Secondary Text | Slate Gray | `#667085` | Deskripsi, metadata, alamat |
| Muted Text | Soft Gray | `#98A2B3` | Placeholder, disabled text |
| Border | Light Border | `#E5E7EB` | Border card, input, divider |
| Section BG | Warm Gray | `#F9FAFB` | Background section sekunder |
| Verified | Growth Green | `#16A34A` | Badge verified, status aktif |
| Link / Map | Connect Blue | `#2563EB` | Link, maps, detail sekunder |
| WhatsApp | WhatsApp Green | `#25D366` | Tombol chat WhatsApp |

### Color Philosophy
**Merah Semangat, Putih Terbuka, Hijau Tumbuh, Biru Terpercaya.**

- **Merah** menggambarkan semangat pelaku UMKM lokal.
- **Putih hangat** memberi kesan bersih, ramah, dan mudah dibaca.
- **Hijau** melambangkan pertumbuhan ekonomi lokal dan status terpercaya.
- **Biru** membangun rasa stabil dan profesional pada link, lokasi, dan kontak.

### Usage Rules
#### Do
- Gunakan `#E60012` hanya untuk CTA penting seperti “Daftarkan UMKM”, “Cari UMKM”, atau highlight brand.
- Gunakan background utama `#FFFDF9` agar halaman terasa hangat dan tidak silau.
- Gunakan `#16A34A` hanya untuk status positif seperti verified/aktif.
- Gunakan `#25D366` khusus untuk tombol WhatsApp.
- Gunakan `#2563EB` untuk link, maps, dan action sekunder.

#### Don't
- Jangan membuat seluruh header atau seluruh halaman menjadi merah penuh.
- Jangan memakai merah untuk semua tombol.
- Jangan mencampur merah, hijau, dan biru terlalu dekat tanpa whitespace.
- Jangan memakai teks merah untuk paragraf panjang.
- Jangan memakai warna terlalu neon atau terlalu gelap untuk user umum.

---

## 5. Typography

### Recommended Font
Gunakan font sans-serif modern yang mudah dibaca.

```css
font-family: Inter, Figtree, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
```

### Typography Scale

| Role | Size Desktop | Size Mobile | Weight | Line Height | Penggunaan |
|---|---:|---:|---:|---:|---|
| Display / Hero | 44px | 32px | 700 | 1.15 | Headline homepage |
| H1 | 36px | 28px | 700 | 1.2 | Judul halaman |
| H2 | 28px | 24px | 700 | 1.25 | Judul section |
| H3 | 22px | 20px | 600 | 1.3 | Judul card/detail |
| Body | 16px | 16px | 400 | 1.7 | Paragraf utama |
| Small Body | 14px | 14px | 400 | 1.6 | Deskripsi pendek |
| Label | 13px | 13px | 500 | 1.4 | Label form/filter |
| Caption | 12px | 12px | 400 | 1.4 | Metadata, badge kecil |
| Button | 14px | 14px | 600 | 1.4 | Teks tombol |

### Typography Rules
- Gunakan minimal `16px` untuk body text agar nyaman dibaca.
- Jangan gunakan body text di bawah `14px`, kecuali caption.
- Heading harus jelas dan tidak terlalu tipis.
- Gunakan `Charcoal #1F2933` untuk heading.
- Gunakan `Slate Gray #667085` untuk deskripsi.
- Hindari paragraf terlalu panjang pada card.

---

## 6. Layout System

### Container
```css
max-width: 1200px;
margin: 0 auto;
padding-left: 24px;
padding-right: 24px;
```

### Mobile Container
```css
padding-left: 16px;
padding-right: 16px;
```

### Spacing Scale
Gunakan skala berbasis 4px:

```txt
4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 48px, 64px, 80px
```

### Section Spacing
| Area | Desktop | Mobile |
|---|---:|---:|
| Hero padding | 80px 0 | 48px 0 |
| Section padding | 64px 0 | 40px 0 |
| Card gap | 20px | 16px |
| Form field gap | 16px | 12px |
| Navbar height | 72px | 64px |

### Grid Rules
#### UMKM / Product Grid
- Desktop: 3–4 columns
- Tablet: 2 columns
- Mobile: 1 column

```css
grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
gap: 20px;
```

### Page Structure
1. Header / Navbar
2. Hero with search
3. Popular categories
4. Featured / verified UMKM
5. Product/service discovery
6. Why use this platform
7. CTA daftar UMKM
8. Footer

---

## 7. Navigation

### Header
Header sebaiknya clean dan tidak terlalu ramai.

#### Desktop
- Logo kiri
- Menu tengah/kiri: Beranda, Kategori, UMKM, Produk/Jasa, Tentang, Kontak
- CTA kanan: “Daftarkan UMKM”
- Optional: Login

#### Mobile
- Logo kiri
- Hamburger kanan
- CTA bisa masuk drawer
- Search tetap muncul di homepage, bukan dipaksa masuk navbar

### Header Style
```css
background: #FFFFFF;
border-bottom: 1px solid #E5E7EB;
height: 72px;
position: sticky;
top: 0;
z-index: 50;
```

### Active State
```css
color: #E60012;
font-weight: 600;
```

### Hover State
```css
color: #9F111B;
```

---

## 8. Search Experience

Search adalah fitur inti platform.

### Search Bar Goals
Search harus bisa menemukan:
- Nama UMKM
- Nama produk/jasa
- Kategori
- Deskripsi singkat
- Tag
- Area/RW
- Status layanan seperti delivery, COD, custom order

### Search UI
Search utama ditampilkan besar di hero section.

#### Desktop Search Layout
- Input keyword besar
- Dropdown kategori
- Dropdown lokasi/RW
- Button “Cari UMKM”

#### Mobile Search Layout
- Input full width
- Filter button membuka bottom sheet/drawer
- Button search full width

### Search Component Recommendation
Gunakan **Livewire** untuk:
- Keyword search
- Filter kategori
- Filter lokasi/RW
- Filter verified
- Sort
- Pagination
- Loading state

Gunakan **Alpine.js** untuk:
- Dropdown open/close
- Filter drawer mobile
- Modal
- Accordion
- Hamburger menu

### Search Behavior
- Gunakan debounce 400–600ms.
- Tampilkan loading state ketika pencarian berlangsung.
- Tampilkan empty state ketika tidak ada hasil.
- Simpan query di URL agar bisa dibagikan.
- Pagination tidak reload halaman penuh.
- Search harus tetap bisa dipakai tanpa JavaScript berat.

### Empty State Copy
```txt
UMKM belum ditemukan.
Coba gunakan kata kunci lain atau pilih kategori yang berbeda.
```

### Suggested Filters
| Filter | Values |
|---|---|
| Kategori | Kuliner, Fashion, Jasa, Toko Harian, Digital, Otomotif, Pendidikan, Kecantikan |
| Lokasi | RW/area sekitar Cimuning |
| Status | Verified, Semua |
| Layanan | Bisa delivery, Bisa COD, Bisa custom order, Toko fisik |
| Sort | Terbaru, A-Z, Populer |

---

## 9. Buttons

### Primary Button
Untuk action utama seperti “Daftarkan UMKM” dan “Cari UMKM”.

```css
background: #E60012;
color: #FFFFFF;
border-radius: 999px;
padding: 12px 20px;
font-size: 14px;
font-weight: 600;
border: 1px solid #E60012;
```

Hover:
```css
background: #9F111B;
border-color: #9F111B;
```

### Secondary Button
Untuk action seperti “Lihat Detail”.

```css
background: #FFFFFF;
color: #E60012;
border: 1px solid #E60012;
border-radius: 999px;
padding: 10px 18px;
font-size: 14px;
font-weight: 600;
```

Hover:
```css
background: #FFE8E8;
```

### WhatsApp Button
Untuk action kontak utama pada detail UMKM.

```css
background: #25D366;
color: #FFFFFF;
border-radius: 999px;
padding: 12px 20px;
font-weight: 600;
```

Hover:
```css
filter: brightness(0.95);
```

### Ghost Button
Untuk action ringan.

```css
background: transparent;
color: #667085;
font-weight: 500;
```

Hover:
```css
color: #1F2933;
```

### Button Rules
- Tinggi minimal tombol mobile: `44px`.
- Jangan memakai warna WhatsApp untuk tombol selain WhatsApp.
- Jangan memakai warna merah untuk action berbahaya seperti delete. Gunakan semantic destructive state sendiri.
- CTA utama hanya satu dalam satu section.

---

## 10. Cards

### UMKM Card
UMKM card harus sederhana, informatif, dan mudah discan.

#### Content
- Foto cover / produk utama
- Nama UMKM
- Badge verified jika ada
- Kategori
- Deskripsi pendek
- Lokasi/RW
- CTA: Lihat Detail / WhatsApp

#### Style
```css
background: #FFFFFF;
border: 1px solid #E5E7EB;
border-radius: 16px;
overflow: hidden;
box-shadow: 0 1px 3px rgba(16, 24, 40, 0.08);
transition: all 180ms ease;
```

Hover:
```css
transform: translateY(-2px);
box-shadow: 0 8px 24px rgba(16, 24, 40, 0.12);
```

Image:
```css
aspect-ratio: 4 / 3;
object-fit: cover;
```

### Product / Service Card
Lebih ringkas dari UMKM card.

#### Content
- Foto produk/jasa
- Nama produk/jasa
- Nama UMKM
- Harga opsional
- Kategori
- CTA detail

### Featured Card
Untuk UMKM verified/populer.

Tambahkan:
- Badge “Verified”
- Badge “Populer”
- Background badge `#FFE8E8`
- Border tetap soft

---

## 11. Badges & Tags

### Category Badge
```css
background: #FFE8E8;
color: #9F111B;
border-radius: 999px;
padding: 6px 12px;
font-size: 12px;
font-weight: 600;
```

### Verified Badge
```css
background: #DCFCE7;
color: #166534;
border-radius: 999px;
padding: 6px 10px;
font-size: 12px;
font-weight: 600;
```

### Service Badge
Untuk “COD”, “Delivery”, “Custom Order”, “Toko Fisik”.

```css
background: #F9FAFB;
color: #667085;
border: 1px solid #E5E7EB;
border-radius: 999px;
padding: 6px 10px;
font-size: 12px;
```

---

## 12. Forms

### Input Style
```css
background: #FFFFFF;
border: 1px solid #E5E7EB;
border-radius: 12px;
padding: 12px 14px;
font-size: 16px;
color: #1F2933;
```

Placeholder:
```css
color: #98A2B3;
```

Focus:
```css
outline: 2px solid rgba(230, 0, 18, 0.18);
border-color: #E60012;
```

### Validation
Error:
```css
border-color: #DC2626;
background: #FFF5F5;
```

Success:
```css
border-color: #16A34A;
```

### Form Rules
- Label selalu ditampilkan, jangan hanya placeholder.
- Error message harus jelas dan pendek.
- Upload image harus membatasi format dan ukuran.
- Form admin harus memiliki preview gambar sebelum submit.
- Form panjang dipecah menjadi section.

---

## 13. Detail UMKM Page

### Layout Desktop
- Kiri: galeri/foto UMKM
- Kanan: nama UMKM, kategori, verified badge, kontak, CTA
- Bawah: deskripsi, produk/jasa, lokasi maps, jam buka, sosial media

### Layout Mobile
- Foto di atas
- Informasi utama
- Sticky bottom CTA: WhatsApp / Hubungi
- Produk/jasa dalam card vertikal

### Important CTA
Pada detail page, CTA utama adalah:
1. Chat WhatsApp
2. Lihat Lokasi
3. Bagikan Profil

### Detail Page Sections
- Hero profile
- Informasi usaha
- Produk/jasa
- Layanan tersedia
- Lokasi
- Jam operasional
- Kontak dan media sosial
- UMKM serupa

---

## 14. Admin & UMKM Dashboard Direction

### Admin Role
Admin dapat:
- Mengelola kategori
- Mengelola data UMKM
- Memverifikasi UMKM
- Mengelola produk/jasa
- Mengelola banner
- Melihat data kontak/leads sederhana

### UMKM Owner Role
UMKM owner dapat:
- Mengedit profil usaha sendiri
- Menambah/mengedit produk/jasa sendiri
- Upload foto
- Melihat status verifikasi
- Melihat jumlah klik WhatsApp jika fitur tersedia

### Dashboard Style
- Sidebar kiri desktop
- Topbar mobile
- Card statistik sederhana
- Table clean
- Action button jelas
- Status badge untuk pending/approved/rejected

### Dashboard Colors
- Gunakan warna netral dominan.
- Merah hanya untuk primary action.
- Hijau untuk approved.
- Kuning untuk pending.
- Merah gelap/destructive untuk reject/delete.

---

## 15. Responsive Behavior

### Breakpoints
| Name | Width | Behavior |
|---|---:|---|
| Mobile | `< 640px` | 1 column, drawer filter, sticky CTA |
| Tablet | `640px–1024px` | 2 columns, compact nav |
| Desktop | `> 1024px` | 3–4 columns, full nav |
| Large Desktop | `> 1280px` | max-width 1200px/1280px |

### Mobile Rules
- Semua tombol utama minimal 44px tinggi.
- Search input full width.
- Filter masuk drawer/bottom sheet.
- Card full width.
- Sticky WhatsApp CTA pada detail UMKM.
- Header jangan terlalu tinggi.
- Hindari tabel lebar di mobile, gunakan card list.

---

## 16. Accessibility Rules

### Contrast
- Teks utama harus kontras tinggi dengan background.
- Jangan pakai teks abu muda di background putih untuk informasi penting.
- CTA merah harus selalu memakai teks putih.

### Focus State
Semua elemen interaktif harus punya focus state.

```css
outline: 2px solid rgba(230, 0, 18, 0.35);
outline-offset: 2px;
```

### Touch Target
- Minimal `44px × 44px`.
- Jarak antar tombol minimal `8px`.

### UX Copy
Gunakan bahasa Indonesia yang sederhana:
- “Cari UMKM”
- “Daftarkan UMKM”
- “Lihat Detail”
- “Chat WhatsApp”
- “Lihat Lokasi”
- “Belum ada produk”
- “Data sedang diverifikasi”

---

## 17. Security-Aware UI Rules

Karena platform memiliki dashboard dan upload data, UI harus mendukung keamanan.

### Upload
- Tampilkan info format file yang diizinkan: JPG, PNG, WEBP.
- Tampilkan maksimal ukuran file.
- Tampilkan preview.
- Jangan menampilkan nama file mentah jika tidak perlu.

### Verification
Status UMKM:
- Pending
- Verified
- Rejected
- Need Revision

### Admin Action
Untuk delete/reject:
- Gunakan confirmation modal.
- Jelaskan dampak aksi.
- Jangan gunakan warna primary red untuk delete; gunakan destructive red yang berbeda.

### Trust Indicators
Tampilkan:
- Badge verified
- Tanggal bergabung
- Area/RW
- Kontak resmi
- Catatan “Transaksi dilakukan langsung dengan UMKM terkait.”

---

## 18. Recommended Laravel Frontend Architecture

### Stack
```txt
Laravel + Blade + Livewire + Alpine.js + Tailwind CSS + MySQL
```

### Responsibilities
| Tool | Responsibility |
|---|---|
| Blade | Layout, page structure, reusable components |
| Livewire | Search, filter, pagination, form interaktif |
| Alpine.js | Dropdown, modal, drawer, hamburger, accordion |
| Tailwind CSS | Styling utility-first |
| MySQL | Data utama |
| Laravel Scout optional | Upgrade search ketika data membesar |

### Suggested Components
```txt
resources/views/components/
├── app-layout.blade.php
├── public-layout.blade.php
├── navbar.blade.php
├── footer.blade.php
├── umkm-card.blade.php
├── product-card.blade.php
├── category-badge.blade.php
├── verified-badge.blade.php
├── empty-state.blade.php
└── primary-button.blade.php
```

### Suggested Livewire Components
```txt
app/Livewire/Public/
├── UmkmSearch.php
├── ProductSearch.php
├── CategoryFilter.php
└── FeaturedUmkm.php

app/Livewire/Dashboard/
├── UmkmForm.php
├── ProductForm.php
└── ImageUploader.php
```

---

## 19. Suggested Pages

### Public Pages
```txt
/
├── Home
/umkm
├── UMKM Listing
/umkm/{slug}
├── Detail UMKM
/kategori/{slug}
├── Category Page
/produk
├── Product/Service Listing
/daftar-umkm
├── Registration Landing/Form
/tentang
├── About
/kontak
└── Contact
```

### Dashboard Pages
```txt
/dashboard
/dashboard/umkm
/dashboard/products
/dashboard/categories
/dashboard/banners
/dashboard/verification
/dashboard/profile
```

---

## 20. Content Guidelines

### Tone
Gunakan bahasa yang:
- Ramah
- Lokal
- Jelas
- Tidak terlalu formal
- Tidak terlalu gaul
- Mudah dipahami pelaku UMKM dan masyarakat umum

### Example Copy
Hero:
```txt
Temukan UMKM Cimuning dengan lebih mudah
Cari makanan, jasa, toko harian, produk kreatif, dan usaha lokal di sekitar Cimuning.
```

Search placeholder:
```txt
Cari produk, jasa, atau nama UMKM...
```

CTA:
```txt
Daftarkan UMKM
```

Trust note:
```txt
Platform ini membantu mempertemukan pembeli dan pelaku UMKM. Transaksi dilakukan langsung dengan pemilik usaha.
```

Empty state:
```txt
Belum ada UMKM pada kategori ini.
Coba pilih kategori lain atau kembali lagi nanti.
```

---

## 21. Do's and Don'ts

### Do
- Buat search bar besar dan mudah ditemukan.
- Prioritaskan tombol WhatsApp pada detail UMKM.
- Gunakan foto produk/usaha dengan ukuran konsisten.
- Gunakan card yang bersih dan tidak terlalu padat.
- Gunakan warna merah sebagai identitas, bukan sebagai warna dominan semua area.
- Gunakan badge verified untuk membangun trust.
- Pastikan mobile experience sangat nyaman.
- Gunakan URL yang jelas dan SEO-friendly.

### Don't
- Jangan membuat flow checkout/payment.
- Jangan menampilkan terlalu banyak filter di awal.
- Jangan membuat desain terlalu ramai.
- Jangan pakai merah terang sebagai background besar terlalu sering.
- Jangan membuat user harus login hanya untuk melihat UMKM.
- Jangan menyembunyikan kontak penjual.
- Jangan membuat card terlalu penuh dengan teks.
- Jangan menghilangkan focus state.

---

## 22. Initial MVP Design Scope

### Must Have
- Homepage
- Search UMKM
- Filter kategori
- UMKM listing
- Detail UMKM
- WhatsApp CTA
- Admin login
- CRUD UMKM
- CRUD kategori
- CRUD produk/jasa
- Upload foto
- Verification status

### Should Have
- Featured UMKM
- Popular categories
- Maps link
- Social media link
- Filter RW/area
- Empty state
- Loading state
- Pagination

### Later
- Review/rating
- Analytics klik WhatsApp
- Artikel/berita UMKM
- Laravel Scout/Meilisearch
- PWA
- QR profile UMKM
- Export data UMKM
- Multi-admin approval flow

---

## 23. Codex Implementation Notes

When generating code, follow these rules:

1. Use Laravel conventions.
2. Keep Blade components reusable.
3. Use Livewire for database-driven interactivity.
4. Use Alpine.js only for lightweight UI behavior.
5. Use Tailwind CSS classes based on this design system.
6. Keep color tokens consistent.
7. Build mobile-first.
8. Use semantic HTML.
9. Add accessible labels and focus states.
10. Avoid payment, checkout, cart, shipping, or transaction flow.
11. Use WhatsApp/contact CTA instead of checkout.
12. Keep UI clean, local, and trust-focused.

---

## 24. Tailwind Token Suggestion

Add these colors to `tailwind.config.js`:

```js
theme: {
  extend: {
    colors: {
      cimuning: {
        red: '#E60012',
        deep: '#9F111B',
        soft: '#FFE8E8',
        white: '#FFFDF9',
        charcoal: '#1F2933',
        slate: '#667085',
        muted: '#98A2B3',
        border: '#E5E7EB',
        section: '#F9FAFB',
        green: '#16A34A',
        blue: '#2563EB',
        whatsapp: '#25D366',
      }
    },
    borderRadius: {
      card: '16px',
      button: '999px',
      input: '12px',
    },
    boxShadow: {
      card: '0 1px 3px rgba(16, 24, 40, 0.08)',
      cardHover: '0 8px 24px rgba(16, 24, 40, 0.12)',
    }
  }
}
```

---

## 25. Final Direction

The final design direction is:

```txt
Local Directory + Clean Marketplace + Community Trust
```

The website should feel like:
- easy for residents,
- useful for UMKM,
- trustworthy for buyers,
- simple for admins,
- scalable for future development.

Main stack:

```txt
Laravel + Blade + Livewire + Alpine.js + Tailwind CSS + MySQL
```

Main UX priority:

```txt
Search → Discover UMKM → View Detail → Contact via WhatsApp/Maps
```

Main visual priority:

```txt
Clean white interface + red brand accent + green verified trust + blue link clarity
```
