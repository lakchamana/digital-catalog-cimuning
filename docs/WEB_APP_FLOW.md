# Web App Flow

## Flow Public User

1. User membuka homepage.
2. Jika pertama kali berkunjung, user melihat interactive walkthrough dan bisa melewati kapan saja.
3. User mencari produk, jasa, kategori, lokasi/RW, atau nama UMKM melalui search utama di navbar.
4. User melihat listing UMKM atau produk/jasa.
5. User membuka detail UMKM.
6. User menghubungi pemilik usaha lewat WhatsApp, telepon, maps, website, atau media sosial.
7. Transaksi dilakukan langsung di luar website.

## Flow UMKM Owner

1. Calon owner membuka `/daftar-umkm`.
2. Owner membuat akun melalui `/admin/register`.
3. Owner login ke dashboard.
4. Owner mengisi atau memperbarui profil UMKM miliknya.
5. UMKM baru tersimpan sebagai pending dan belum tampil publik.
6. Owner menambah produk/jasa dan foto setelah profil UMKM tersedia.
7. Owner melihat status pending, verified, rejected, atau need revision.
8. Owner memperbaiki data jika admin meminta revisi.

## Flow Admin

1. Admin login ke dashboard.
2. Admin melihat data UMKM yang masuk.
3. Admin memeriksa profil, kontak, kategori, foto, dan produk/jasa.
4. Admin mengubah status verifikasi.
5. Admin mengelola kategori dan data yang perlu dikoreksi.

## Flow Pencarian UMKM

1. User mengetik keyword di search bar.
2. Livewire menjalankan pencarian dengan debounce sekitar 400-600ms.
3. User dapat memfilter berdasarkan kategori, lokasi/RW, verified, layanan, dan sort.
4. Query search disimpan di URL agar bisa dibagikan.
5. Jika tidak ada hasil, tampilkan empty state yang ramah.

## Flow Pendaftaran UMKM

1. User membuka `/daftar-umkm`.
2. User membaca manfaat dan langkah pendaftaran account-first.
3. User membuat akun owner dari `/admin/register`.
4. Setelah login, owner melengkapi data usaha, kategori, kontak, alamat, layanan, dan foto dari dashboard.
5. Sistem memvalidasi input dan upload.
6. Sistem membuat slug unik dan menyimpan data dengan status pending serta belum aktif.
7. Sistem mengirim notifikasi dashboard ke admin.
8. Admin melakukan verifikasi dari dashboard.

## Flow Homepage Product-Led

1. User membuka homepage.
2. User langsung melihat search besar di navbar sebagai navigasi utama discovery.
3. User melihat carousel jumbotron informatif berisi produk lokal, UMKM verified, akun owner, dan katalog digital.
4. User memilih ikon kategori cepat, termasuk "Lihat Semua" untuk membuka `/kategori`.
5. User melihat produk/jasa terbaru dan dapat membuka profil UMKM atau bertanya lewat WhatsApp.
6. Website mencatat klik WhatsApp/Maps sebagai lead anonim.
7. Tidak ada cart, checkout, payment, ongkir, atau transaksi internal.

## Flow Semua Kategori

1. User menekan ikon atau link "Lihat Semua" pada kategori homepage.
2. Sistem membuka `/kategori`.
3. User melihat semua kategori aktif dalam grid ikon, deskripsi singkat, dan jumlah UMKM verified.
4. User memilih kategori.
5. Sistem membuka `/produk?category={slug}` untuk menampilkan produk/jasa sesuai kategori.

## Flow First-Visit Tutorial

1. Visitor baru membuka halaman publik yang memakai public layout.
2. Sistem mengecek localStorage `cimuning_walkthrough_seen_v1`.
3. Jika belum pernah melihat tutorial, modal/bottom sheet membuka interactive walkthrough.
4. User dapat memulai panduan, mencoba search produk/jasa, membuka kategori/produk, atau menuju pendaftaran UMKM.
5. User dapat memilih "Skip", "Selesai", atau tombol tutup kapan saja.
6. Setelah ditutup, sistem menyimpan status di browser agar walkthrough tidak muncul lagi.

## Flow Verifikasi UMKM

1. Admin membuka dashboard verification.
2. Admin memeriksa UMKM pending.
3. Admin memilih verified, rejected, atau need revision.
4. Status verified mengaktifkan UMKM agar tampil publik.
5. Status rejected atau need revision menonaktifkan UMKM dari tampilan publik.
6. Owner menerima notifikasi dashboard jika UMKM sudah memiliki akun/assignment.
7. Owner melihat status terbaru di dashboard jika sudah memiliki akun/assignment.
8. Public hanya melihat UMKM yang aktif dan verified.

## Flow Notifikasi Dashboard

1. Admin atau owner login ke `/admin`.
2. Filament menampilkan notification bell dengan polling berkala.
3. Admin menerima notifikasi pendaftaran UMKM baru dari form publik.
4. Owner menerima notifikasi ketika UMKM miliknya verified, need revision, atau rejected.
5. Notifikasi mengarah ke halaman edit UMKM di dashboard.
6. Notifikasi tahap MVP hanya tersimpan di database, tanpa email, WhatsApp, atau realtime broadcast.

## Flow Tambah Produk/Jasa

1. Owner membuka dashboard products.
2. Owner menambah nama, kategori, deskripsi, harga opsional, gambar, dan status aktif.
3. Sistem memvalidasi data dan upload gambar.
4. Produk tampil di detail UMKM dan listing produk jika aktif.

## Flow Kontak WhatsApp/Maps

1. User membuka detail UMKM.
2. User menekan CTA WhatsApp atau Lihat Lokasi.
3. Website mencatat klik sebagai lead anonim.
4. Website mengarahkan user ke WhatsApp atau maps.
5. Transaksi tetap dilakukan langsung di luar website.

## Flow Analytics Lead

1. Admin atau owner login ke `/admin`.
2. Dashboard menampilkan ringkasan klik WhatsApp, klik Maps, klik 7 hari terakhir, dan UMKM paling diminati.
3. Admin melihat semua aktivitas lead terbaru.
4. Owner hanya melihat aktivitas lead dari UMKM miliknya.
5. Data lead dipakai sebagai indikator minat kontak/lokasi, bukan transaksi internal.
