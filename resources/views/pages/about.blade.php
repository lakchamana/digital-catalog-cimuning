<x-public-layout
    title="Tentang Cimuning Digital Hub"
    description="Cimuning Digital Hub adalah direktori UMKM lokal Cimuning untuk menemukan produk, jasa, lokasi Google Maps, QR profil, dan kontak langsung pelaku usaha."
    :canonical="route('about')"
>
    <section class="bg-cimuning-section">
        <div class="container-cimuning grid gap-8 py-12 md:grid-cols-[1.1fr_0.9fr] md:items-center md:py-16">
            <div>
                <x-category-badge>Direktori UMKM Cimuning</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Tentang Cimuning Digital Hub</h1>
                <p class="mt-5 max-w-3xl text-base leading-8 text-cimuning-slate md:text-lg">
                    Cimuning Digital Hub membantu warga menemukan UMKM lokal, melihat katalog produk atau jasa, membuka lokasi Google Maps, dan menghubungi pemilik usaha secara langsung.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-primary-button href="{{ route('products.index') }}">Cari Produk/Jasa</x-primary-button>
                    <x-secondary-button href="{{ route('umkm.index') }}">Lihat Direktori UMKM</x-secondary-button>
                </div>
            </div>

            <div class="rounded-card border border-cimuning-border bg-white p-6 shadow-card">
                <h2 class="text-xl font-bold text-cimuning-charcoal">Prinsip platform</h2>
                <div class="mt-5 grid gap-4">
                    @foreach ([
                        ['title' => 'Discovery lokal', 'body' => 'Fokus membantu warga menemukan kebutuhan dari pelaku usaha di sekitar Cimuning.'],
                        ['title' => 'Kontak langsung', 'body' => 'Website mengarahkan ke WhatsApp, Maps, website, atau media sosial pemilik usaha.'],
                        ['title' => 'Bukan marketplace transaksi', 'body' => 'Tidak ada cart, checkout, payment, ongkir, atau transaksi internal di website.'],
                    ] as $item)
                        <div class="rounded-card border border-cimuning-border bg-cimuning-section p-4">
                            <h3 class="font-semibold text-cimuning-charcoal">{{ $item['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-cimuning-slate">{{ $item['body'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 md:py-16">
        <div class="container-cimuning">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Cara kerja</p>
                <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal md:text-3xl">Dari pencarian sampai kontak usaha</h2>
                <p class="mt-3 text-base leading-8 text-cimuning-slate">
                    Alur dibuat sederhana agar bisa dipakai warga tanpa login dan tetap memberi ruang bagi owner UMKM untuk mengelola data usahanya.
                </p>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['step' => '01', 'title' => 'Cari kebutuhan', 'body' => 'Gunakan search, kategori, produk/jasa, atau direktori UMKM.'],
                    ['step' => '02', 'title' => 'Buka profil', 'body' => 'Lihat deskripsi, layanan, katalog produk, QR profil, dan status verified.'],
                    ['step' => '03', 'title' => 'Cek lokasi', 'body' => 'Buka Google Maps dari profil untuk melihat alamat atau titik usaha.'],
                    ['step' => '04', 'title' => 'Hubungi langsung', 'body' => 'Lanjutkan percakapan dan transaksi langsung dengan pemilik UMKM.'],
                ] as $item)
                    <article class="rounded-card border border-cimuning-border bg-cimuning-section p-5">
                        <span class="text-sm font-bold text-cimuning-red">{{ $item['step'] }}</span>
                        <h3 class="mt-3 text-lg font-bold text-cimuning-charcoal">{{ $item['title'] }}</h3>
                        <p class="mt-2 text-sm leading-6 text-cimuning-slate">{{ $item['body'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-cimuning-section py-12 md:py-16">
        <div class="container-cimuning grid gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Untuk siapa</p>
                <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal md:text-3xl">Manfaat untuk warga dan UMKM</h2>
            </div>

            <div class="grid gap-5 lg:col-span-2 md:grid-cols-2">
                <article class="rounded-card border border-cimuning-border bg-white p-6 shadow-card">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">Untuk warga</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Temukan produk, jasa, toko, katering, layanan rumahan, dan usaha lokal tanpa harus login. Semua diarahkan ke kontak langsung.
                    </p>
                    <x-secondary-button href="{{ route('products.index') }}" class="mt-5">Mulai Jelajah</x-secondary-button>
                </article>

                <article class="rounded-card border border-cimuning-border bg-white p-6 shadow-card">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">Untuk owner UMKM</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Buat akun owner, lengkapi profil usaha, tambah produk atau jasa, lalu tunggu verifikasi admin agar profil tampil publik.
                    </p>
                    <x-primary-button href="{{ route('umkm.register') }}" class="mt-5">Daftarkan UMKM</x-primary-button>
                </article>
            </div>
        </div>
    </section>
</x-public-layout>
