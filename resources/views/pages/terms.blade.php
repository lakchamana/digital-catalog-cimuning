@php
    $supportEmail = (string) config('support.email');
    $supportPhone = (string) config('support.whatsapp_display');
    $supportWhatsappUrl = 'https://wa.me/'.preg_replace('/\D+/', '', (string) config('support.whatsapp')).'?text='.
        rawurlencode((string) config('support.whatsapp_message'));
@endphp

<x-public-layout
    title="Syarat Penggunaan"
    description="Syarat Penggunaan Cimuning Digital Hub untuk owner UMKM, pengelolaan katalog, moderasi, kontak langsung, dan tanggung jawab penggunaan layanan."
    :canonical="route('terms')"
>
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-12 md:py-16">
            <div class="max-w-3xl">
                <x-category-badge>Ketentuan Layanan</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Syarat Penggunaan Cimuning Digital Hub</h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate md:text-lg">
                    Syarat ini mengatur penggunaan akun owner, profil UMKM, dan katalog produk atau jasa. Pengunjung publik dapat memakai direktori tanpa membuat akun.
                </p>
                <p class="mt-4 text-sm leading-6 text-cimuning-slate">Berlaku sejak: 29 Juni 2026.</p>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 md:py-16">
        <div class="container-cimuning grid gap-6 lg:grid-cols-[280px_1fr] lg:items-start">
            <aside class="rounded-card border border-cimuning-border bg-cimuning-section p-5 lg:sticky lg:top-24">
                <h2 class="text-base font-bold text-cimuning-charcoal">Ringkasan</h2>
                <nav class="mt-4 space-y-2 text-sm text-cimuning-slate" aria-label="Daftar isi syarat penggunaan">
                    @foreach ([
                        'ruang-lingkup' => 'Ruang lingkup',
                        'kewajiban-owner' => 'Kewajiban owner',
                        'konten-media' => 'Konten dan media',
                        'larangan' => 'Konten terlarang',
                        'verifikasi' => 'Verifikasi dan moderasi',
                        'transaksi' => 'Transaksi langsung',
                        'keamanan-akun' => 'Keamanan akun',
                        'penangguhan' => 'Penangguhan akun',
                        'perubahan-kontak' => 'Perubahan dan kontak',
                    ] as $id => $label)
                        <a href="#{{ $id }}" class="block rounded-button px-3 py-2 hover:bg-white hover:text-cimuning-red focus:outline-2">{{ $label }}</a>
                    @endforeach
                </nav>
            </aside>

            <div class="space-y-6">
                <article id="ruang-lingkup" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">1. Ruang lingkup layanan</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Cimuning Digital Hub menyediakan direktori UMKM, katalog informasi produk atau jasa, lokasi, dan jalur kontak langsung. Platform tidak menyediakan keranjang, checkout, pembayaran, escrow, ongkir, atau transaksi internal.
                    </p>
                </article>

                <article id="kewajiban-owner" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">2. Kewajiban owner</h2>
                    <ul class="mt-4 space-y-3 text-base leading-8 text-cimuning-slate">
                        <li>Memiliki kewenangan untuk mendaftarkan dan mengelola usaha yang diajukan.</li>
                        <li>Memberikan informasi usaha, kontak, lokasi, harga, layanan, dan produk secara benar serta tidak menyesatkan.</li>
                        <li>Memperbarui informasi yang sudah tidak berlaku dan menanggapi permintaan revisi dari admin.</li>
                        <li>Mematuhi hukum Indonesia dan ketentuan yang berlaku bagi jenis usaha masing-masing.</li>
                    </ul>
                </article>

                <article id="konten-media" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">3. Konten, foto, dan hak penggunaan</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Owner tetap memiliki hak atas konten yang diunggah dan menyatakan bahwa konten tersebut milik sendiri atau digunakan dengan izin yang sah. Owner memberi Cimuning Digital Hub izin non-eksklusif untuk menyimpan, menampilkan, menyesuaikan format, dan mempromosikan konten tersebut hanya untuk pengoperasian direktori dan katalog.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        Owner bertanggung jawab menghapus atau mengganti konten yang melanggar hak cipta, merek, privasi, atau hak pihak lain.
                    </p>
                </article>

                <article id="larangan" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">4. Konten dan penggunaan yang dilarang</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">Owner tidak boleh menggunakan platform untuk:</p>
                    <ul class="mt-3 space-y-3 text-base leading-8 text-cimuning-slate">
                        <li>Menawarkan barang atau jasa yang dilarang hukum, palsu, berbahaya, atau tidak memiliki izin yang diwajibkan.</li>
                        <li>Memuat penipuan, spam, informasi palsu, pornografi, kebencian, diskriminasi, ancaman, atau eksploitasi.</li>
                        <li>Mengunggah malware, mencoba mengakses akun lain, mengganggu layanan, atau menyalahgunakan data pengguna.</li>
                        <li>Melanggar hak kekayaan intelektual, privasi, atau hak hukum pihak lain.</li>
                    </ul>
                </article>

                <article id="verifikasi" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">5. Verifikasi dan moderasi</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Profil baru dan perubahan tertentu dapat ditinjau sebelum tampil. Admin dapat meminta revisi, menolak pengajuan, menonaktifkan publikasi, atau memblokir produk dengan alasan yang dicatat. Badge verified berarti profil telah ditinjau, bukan jaminan kualitas produk, legalitas seluruh kegiatan, atau keberhasilan transaksi.
                    </p>
                </article>

                <article id="transaksi" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">6. Komunikasi dan transaksi langsung</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Pertanyaan, pemesanan, pembayaran, pengiriman, pembatalan, dan penyelesaian keluhan dilakukan langsung antara pengguna dan owner di luar platform. Masing-masing pihak bertanggung jawab memeriksa identitas, harga, kualitas, keamanan, dan kesepakatan transaksi tanpa mengurangi hak konsumen yang dijamin hukum.
                    </p>
                    <p class="mt-3 text-base leading-8 text-cimuning-slate">
                        WhatsApp, Google Maps, media sosial, website owner, hosting, dan penyimpanan media adalah layanan pihak ketiga dengan ketentuan masing-masing.
                    </p>
                </article>

                <article id="keamanan-akun" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">7. Keamanan akun</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Owner wajib menjaga password, menggunakan email miliknya sendiri, dan segera menghubungi pengelola bila menduga akun disalahgunakan. Aktivitas yang dilakukan melalui akun dianggap berasal dari pemilik akun sampai pengelola menerima laporan dan dapat melakukan pengamanan.
                    </p>
                </article>

                <article id="penangguhan" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">8. Penangguhan, penonaktifan, dan penghapusan</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Akses atau publikasi dapat ditangguhkan untuk keamanan, pelanggaran ketentuan, kewajiban hukum, atau pemeriksaan lebih lanjut. Owner dapat meminta koreksi, penonaktifan, atau penghapusan data melalui kontak resmi. Pemrosesan data mengikuti Kebijakan Privasi dan kebutuhan audit minimum yang sah.
                    </p>
                </article>

                <article id="perubahan-kontak" class="rounded-card border border-cimuning-border p-6 shadow-card scroll-mt-24">
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">9. Perubahan ketentuan dan kontak</h2>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">
                        Ketentuan dapat diperbarui jika layanan, aturan, atau kebutuhan operasional berubah. Versi dan tanggal terbaru akan ditampilkan pada halaman ini. Ketentuan ini mengikuti hukum Republik Indonesia.
                    </p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <a href="mailto:{{ $supportEmail }}" class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border px-5 py-3 text-sm font-semibold text-cimuning-charcoal hover:border-cimuning-red hover:text-cimuning-red focus:outline-2">
                            {{ $supportEmail }}
                        </a>
                        <a href="{{ $supportWhatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-whatsapp px-5 py-3 text-sm font-semibold text-white hover:brightness-95 focus:outline-2">
                            WhatsApp {{ $supportPhone }}
                        </a>
                    </div>
                    <a href="{{ route('privacy') }}" class="mt-5 inline-block text-sm font-semibold text-cimuning-red underline underline-offset-4">Baca Kebijakan Privasi</a>
                </article>
            </div>
        </div>
    </section>
</x-public-layout>
