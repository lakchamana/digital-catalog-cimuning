@php
    $categories = [
        ['name' => 'Kuliner', 'count' => '24 usaha'],
        ['name' => 'Fashion', 'count' => '12 usaha'],
        ['name' => 'Jasa', 'count' => '18 usaha'],
        ['name' => 'Toko Harian', 'count' => '15 usaha'],
        ['name' => 'Kecantikan', 'count' => '9 usaha'],
        ['name' => 'Digital', 'count' => '7 usaha'],
    ];

    $featuredUmkms = [
        [
            'name' => 'Dapur Ibu Sari',
            'category' => 'Kuliner',
            'description' => 'Aneka nasi box, kue basah, dan pesanan harian untuk warga sekitar Cimuning.',
            'location' => 'Cimuning RW 03',
            'imageClass' => 'from-cimuning-soft via-white to-orange-50',
        ],
        [
            'name' => 'Bengkel Berkah Motor',
            'category' => 'Otomotif',
            'description' => 'Servis ringan, ganti oli, dan perawatan motor dengan layanan cepat dan ramah.',
            'location' => 'Cimuning RW 06',
            'imageClass' => 'from-blue-50 via-white to-cimuning-section',
        ],
        [
            'name' => 'Kriya Cimuning',
            'category' => 'Produk Kreatif',
            'description' => 'Kerajinan lokal, souvenir custom, dan hampers untuk acara keluarga maupun komunitas.',
            'location' => 'Cimuning RW 01',
            'imageClass' => 'from-green-50 via-white to-cimuning-soft',
        ],
    ];
@endphp

<x-public-layout title="Beranda">
    <section class="bg-cimuning-white">
        <div class="container-cimuning grid gap-10 py-12 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:py-20">
            <div>
                <x-category-badge>Direktori UMKM Cimuning</x-category-badge>
                <h1 class="mt-5 text-4xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">
                    Temukan UMKM Cimuning dengan lebih mudah
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-cimuning-slate md:text-lg">
                    Cari makanan, jasa, toko harian, produk kreatif, dan usaha lokal di sekitar Cimuning. Hubungi pemilik usaha langsung lewat WhatsApp, maps, atau media sosial.
                </p>

                <form action="{{ route('umkm.index') }}" method="GET" class="mt-8 rounded-card border border-cimuning-border bg-white p-4 shadow-card md:p-5">
                    <label for="search" class="text-sm font-semibold text-cimuning-charcoal">Cari produk, jasa, atau nama UMKM</label>
                    <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
                        <input id="search" name="search" type="search" placeholder="Cari produk, jasa, atau nama UMKM..." class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:border-cimuning-red focus:outline-2" />
                        <x-primary-button class="w-full md:w-auto">Cari UMKM</x-primary-button>
                    </div>
                    <p class="mt-3 text-sm leading-6 text-cimuning-slate">
                        Platform ini membantu mempertemukan pembeli dan pelaku UMKM. Transaksi dilakukan langsung dengan pemilik usaha.
                    </p>
                </form>

                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <x-secondary-button href="{{ route('umkm.index') }}">Lihat Direktori</x-secondary-button>
                    <x-primary-button href="{{ route('umkm.register') }}">Daftarkan UMKM</x-primary-button>
                </div>
            </div>

            <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card">
                <div class="grid gap-4">
                    <div class="rounded-card bg-cimuning-section p-5">
                        <p class="text-sm font-semibold text-cimuning-red">Alur sederhana</p>
                        <div class="mt-4 grid gap-3">
                            @foreach (['Cari kebutuhan', 'Buka profil UMKM', 'Hubungi langsung'] as $step)
                                <div class="flex items-center gap-3 rounded-xl bg-white p-4 shadow-card">
                                    <span class="flex h-9 w-9 items-center justify-center rounded-button bg-cimuning-soft text-sm font-bold text-cimuning-deep">{{ $loop->iteration }}</span>
                                    <span class="font-semibold text-cimuning-charcoal">{{ $step }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="rounded-card border border-cimuning-border bg-white p-4">
                            <p class="text-2xl font-bold text-cimuning-charcoal">80+</p>
                            <p class="mt-1 text-sm text-cimuning-slate">UMKM potensial</p>
                        </div>
                        <div class="rounded-card border border-cimuning-border bg-white p-4">
                            <p class="text-2xl font-bold text-cimuning-green">Verified</p>
                            <p class="mt-1 text-sm text-cimuning-slate">Status kepercayaan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-cimuning-charcoal md:text-3xl">Kategori populer</h2>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">Mulai jelajahi UMKM berdasarkan kebutuhan warga sehari-hari.</p>
                </div>
                <a href="{{ route('umkm.index') }}" class="text-sm font-semibold text-cimuning-blue hover:underline">Lihat semua kategori</a>
            </div>

            <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <a href="{{ route('umkm.index', ['category' => $category['name']]) }}" class="rounded-card border border-cimuning-border bg-cimuning-white p-5 shadow-card transition hover:-translate-y-0.5 hover:shadow-card-hover">
                        <span class="text-lg font-bold text-cimuning-charcoal">{{ $category['name'] }}</span>
                        <span class="mt-2 block text-sm text-cimuning-slate">{{ $category['count'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-cimuning-section py-10 md:py-16">
        <div class="container-cimuning">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-bold text-cimuning-charcoal md:text-3xl">UMKM pilihan</h2>
                <p class="mt-2 text-base leading-7 text-cimuning-slate">Contoh tampilan card untuk UMKM verified. Data ini sementara sampai database dan Livewire search dibuat.</p>
            </div>

            <div class="mt-7 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredUmkms as $umkm)
                    <x-umkm-card :name="$umkm['name']" :category="$umkm['category']" :description="$umkm['description']" :location="$umkm['location']" :image-class="$umkm['imageClass']" />
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning">
            <div class="rounded-card border border-cimuning-border bg-cimuning-charcoal p-6 text-white md:p-10">
                <div class="grid gap-6 md:grid-cols-[1fr_auto] md:items-center">
                    <div>
                        <h2 class="text-2xl font-bold md:text-3xl">Punya usaha di Cimuning?</h2>
                        <p class="mt-3 max-w-2xl text-base leading-8 text-white/80">
                            Daftarkan UMKM Anda agar lebih mudah ditemukan warga. Admin akan membantu proses verifikasi data pada tahap dashboard berikutnya.
                        </p>
                    </div>
                    <x-primary-button href="{{ route('umkm.register') }}" class="bg-cimuning-red hover:bg-cimuning-deep">Daftarkan UMKM</x-primary-button>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
