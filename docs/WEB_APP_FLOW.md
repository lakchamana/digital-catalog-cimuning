# Web App Flow

## Flow Public User

1. User membuka homepage.
2. User mencari produk, jasa, kategori, lokasi/RW, atau nama UMKM.
3. User melihat listing UMKM atau produk/jasa.
4. User membuka detail UMKM.
5. User menghubungi pemilik usaha lewat WhatsApp, telepon, maps, website, atau media sosial.
6. Transaksi dilakukan langsung di luar website.

## Flow UMKM Owner

1. Owner login ke dashboard.
2. Owner mengisi atau memperbarui profil UMKM miliknya.
3. Owner menambah produk/jasa dan foto.
4. Owner mengirim data untuk diverifikasi.
5. Owner melihat status pending, verified, rejected, atau need revision.
6. Owner memperbaiki data jika admin meminta revisi.

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
2. User mengisi data usaha, kategori, kontak, alamat, layanan, dan foto.
3. Sistem memvalidasi input dan upload.
4. Sistem membuat slug unik dan menyimpan data dengan status pending serta belum aktif.
5. Sistem mengirim notifikasi dashboard ke admin.
6. Akun owner tidak dibuat otomatis pada tahap ini.
7. Admin melakukan verifikasi dari dashboard.

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
3. Website mengarahkan user ke WhatsApp atau maps.
4. Pada fase berikutnya, klik dapat dicatat sebagai lead sederhana.
