<x-public-layout
    title="Kebijakan Privasi"
    description="Kebijakan Privasi Cimuning Digital Hub tentang pengumpulan, penggunaan, publikasi, penyimpanan, dan hak pengguna atas data pribadi."
    :canonical="route('privacy')"
>
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-12 md:py-16">
            <div class="max-w-3xl">
                <x-category-badge>Privasi Pengguna</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Kebijakan Privasi Cimuning Digital Hub</h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate md:text-lg">
                    Kebijakan ini menjelaskan bagaimana Cimuning Digital Hub mengelola data pengunjung, owner UMKM, dan admin. Platform ini adalah direktori dan katalog digital UMKM, bukan marketplace transaksi.
                </p>
                <p class="mt-4 text-sm leading-6 text-cimuning-slate">
                    Terakhir diperbarui: 24 Juni 2026. Kebijakan ini disusun dengan memperhatikan prinsip pelindungan data pribadi dalam UU No. 27 Tahun 2022.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 md:py-16">
        <div class="container-cimuning grid gap-6 lg:grid-cols-[280px_1fr] lg:items-start">
            <aside class="rounded-card border border-cimuning-border bg-cimuning-section p-5 lg:sticky lg:top-24">
                <h2 class="text-base font-bold text-cimuning-charcoal">Ringkasan</h2>
                <nav class="mt-4 space-y-2 text-sm text-cimuning-slate" aria-label="Daftar isi kebijakan privasi">
                    @foreach ([
                        'siapa-kami' => 'Siapa kami',
                        'data-dikumpulkan' => 'Data yang dikelola',
                        'tujuan' => 'Tujuan penggunaan',
                        'data-publik' => 'Data yang tampil publik',
                        'pihak-ketiga' => 'Pihak ketiga',
                        'hak-pengguna' => 'Hak pengguna',
                        'keamanan-retensi' => 'Keamanan dan retensi',
                        'kontak' => 'Kontak',
                    ] as $id => $label)
                        <a href="#{{ $id }}" class="block rounded-button px-3 py-2 hover:bg-white hover:text-cimuning-red focus:outline-2">{{ $label }}</a>
                    @endforeach
                </nav>
            </aside>

            <div class="space-y-6">
                <article id="siapa-kami" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">1. Siapa kami</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Cimuning Digital Hub adalah platform direktori UMKM lokal Cimuning yang membantu masyarakat menemukan profil usaha, katalog produk atau jasa, lokasi Google Maps, dan jalur kontak langsung ke pelaku usaha.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Website ini tidak menyediakan keranjang belanja, checkout, payment gateway, escrow, ongkir otomatis, atau transaksi internal. Komunikasi dan transaksi dilakukan langsung antara pengguna dan pemilik UMKM di luar website.
                    </p>
                </article>

                <article id="data-dikumpulkan" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">2. Data yang dikelola</h2>
                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="rounded-card border border-cimuning-border bg-cimuning-section p-4">
                            <h3 class="font-bold text-cimuning-charcoal">Pengunjung publik</h3>
                            <p class="mt-2 text-sm leading-6 text-cimuning-slate">
                                Kata kunci dan filter yang muncul di URL, session teknis browser, localStorage untuk walkthrough dan pemberitahuan privasi, serta data yang diproses browser saat membuka link eksternal seperti WhatsApp atau Google Maps.
                            </p>
                        </div>
                        <div class="rounded-card border border-cimuning-border bg-cimuning-section p-4">
                            <h3 class="font-bold text-cimuning-charcoal">Owner UMKM</h3>
                            <p class="mt-2 text-sm leading-6 text-cimuning-slate">
                                Nama, email, password terenkripsi, data UMKM, kontak usaha, WhatsApp, alamat, RW, koordinat Maps, media sosial, produk, harga, logo, cover, galeri, status verifikasi, notifikasi dashboard, dan catatan moderasi.
                            </p>
                        </div>
                        <div class="rounded-card border border-cimuning-border bg-cimuning-section p-4 md:col-span-2">
                            <h3 class="font-bold text-cimuning-charcoal">Keamanan panel pengelola</h3>
                            <p class="mt-2 text-sm leading-6 text-cimuning-slate">
                                Sistem mencatat login/logout admin, login panel yang gagal, akses sensitif yang ditolak, perubahan profil admin, dan perubahan kategori. Login gagal memakai hash identitas agar email percobaan tidak disimpan sebagai teks terbuka. Log tidak menyimpan password, token, secret, IP mentah, query pencarian, atau isi file media.
                            </p>
                        </div>
                    </div>
                </article>

                <article id="tujuan" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">3. Tujuan penggunaan data</h2>
                    <ul class="mt-4 space-y-3 text-base leading-8 text-cimuning-slate">
                        <li>Menampilkan direktori UMKM dan katalog produk/jasa yang sudah diverifikasi.</li>
                        <li>Mengelola akun owner, pengajuan UMKM, revisi, verifikasi, dan notifikasi dashboard.</li>
                        <li>Mengamankan akun, membatasi akses berdasarkan role, dan mencegah penyalahgunaan form.</li>
                        <li>Menyediakan audit keamanan panel untuk penyelidikan akses dan perubahan administratif.</li>
                        <li>Menyimpan foto dan media yang owner unggah untuk kebutuhan profil dan katalog.</li>
                        <li>Menjalankan moderasi produk atau profil yang melanggar ketentuan katalog publik.</li>
                    </ul>
                </article>

                <article id="data-publik" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">4. Data yang tampil publik</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Setelah UMKM disetujui admin, data yang dapat tampil publik mencakup nama UMKM, kategori, deskripsi, layanan, kontak usaha, WhatsApp, alamat/RW, titik Google Maps bila ada, media sosial, website, QR profil, produk/jasa, harga bila diisi, dan foto yang diunggah owner.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Data owner seperti email login, password, notifikasi internal, catatan revisi, dan log moderasi tidak ditampilkan sebagai informasi publik.
                    </p>
                </article>

                <article id="pihak-ketiga" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">5. Pihak ketiga dan layanan eksternal</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Website dapat menggunakan Railway untuk hosting dan database, Cloudinary untuk penyimpanan media, serta link eksternal menuju WhatsApp, Google Maps, website, Instagram, atau TikTok. Saat pengguna membuka layanan eksternal, kebijakan privasi layanan tersebut berlaku di luar kendali Cimuning Digital Hub.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Cimuning Digital Hub tidak menyimpan tracking klik WhatsApp/Maps, scan QR, IP hash pengunjung, user agent, referer, atau analytics kontak di database.
                    </p>
                </article>

                <article id="hak-pengguna" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">6. Hak pengguna dan owner</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Pengguna dan owner dapat meminta akses, koreksi, pembaruan, penonaktifan, atau penghapusan data sejauh memungkinkan secara teknis dan tidak bertentangan dengan kebutuhan administrasi, keamanan, audit moderasi, atau kewajiban hukum.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Owner dapat memperbarui data UMKM dan produk melalui dashboard. Perubahan pada profil yang sudah verified dapat menunggu review admin sebelum tampil publik.
                    </p>
                </article>

                <article id="keamanan-retensi" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">7. Keamanan, penyimpanan, dan retensi</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Sistem memakai password hashing, akses berbasis role, verifikasi admin read-only, pembatasan upload, pemeriksaan tipe file, audit moderasi, dan log aktivitas keamanan panel untuk membantu menjaga keamanan data.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Data akun, profil UMKM, produk, media, notifikasi, log moderasi, dan log aktivitas admin disimpan selama masih diperlukan untuk menjalankan layanan, menyelesaikan pengajuan, menjaga keamanan katalog, atau memenuhi kebutuhan audit internal. Data dapat dinonaktifkan atau dihapus berdasarkan permintaan yang sah, dengan tetap mempertahankan catatan minimum yang diperlukan untuk keamanan atau kewajiban hukum.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Database dapat disalin ke backup terenkripsi untuk pemulihan insiden, sedangkan media dilindungi melalui backup penyedia penyimpanan. Akses backup dibatasi kepada admin, passphrase disimpan terpisah, dan salinan lama dihapus mengikuti jadwal retensi. Data yang telah dihapus dari layanan aktif dapat tetap berada sementara di dalam backup sampai masa retensinya berakhir; data tersebut tidak digunakan kembali kecuali untuk pemulihan yang sah dan akan ditinjau ulang setelah restore.
                    </p>
                </article>

                <article id="kontak" class="rounded-card border border-cimuning-border bg-white p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">8. Kontak dan pembaruan kebijakan</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Kontak resmi pengelola akan diumumkan oleh tim Cimuning Digital Hub setelah kanal resmi ditetapkan. Untuk saat ini, gunakan halaman Kontak & Bantuan agar tidak ada nomor atau email sementara yang membingungkan pengguna.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Kebijakan ini dapat diperbarui jika ada perubahan fitur, layanan pihak ketiga, atau kebutuhan hukum. Jika nanti website menambahkan analytics, email marketing, payment, chat, atau tracking baru, kebijakan dan mekanisme persetujuan harus ditinjau ulang.
                    </p>
                    <x-secondary-button href="{{ route('contact') }}" class="mt-5">Buka Kontak & Bantuan</x-secondary-button>
                </article>

                <div class="rounded-card border border-cimuning-border bg-cimuning-section p-5 text-sm leading-6 text-cimuning-slate">
                    Dokumen ini adalah kebijakan operasional platform dan bukan pengganti nasihat hukum profesional. Review hukum formal tetap disarankan sebelum penggunaan skala luas.
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
