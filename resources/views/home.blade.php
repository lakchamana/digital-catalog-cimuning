@php
    $fallbackCategories = collect([
        (object) ['name' => 'Kuliner', 'slug' => 'kuliner', 'umkms_count' => 24],
        (object) ['name' => 'Fashion', 'slug' => 'fashion', 'umkms_count' => 12],
        (object) ['name' => 'Jasa', 'slug' => 'jasa', 'umkms_count' => 18],
        (object) ['name' => 'Toko Harian', 'slug' => 'toko-harian', 'umkms_count' => 15],
        (object) ['name' => 'Kecantikan', 'slug' => 'kecantikan', 'umkms_count' => 9],
        (object) ['name' => 'Digital', 'slug' => 'digital', 'umkms_count' => 7],
        (object) ['name' => 'Otomotif', 'slug' => 'otomotif', 'umkms_count' => 10],
        (object) ['name' => 'Produk Kreatif', 'slug' => 'produk-kreatif', 'umkms_count' => 8],
        (object) ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'umkms_count' => 5],
        (object) ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'umkms_count' => 6],
        (object) ['name' => 'Laundry', 'slug' => 'laundry', 'umkms_count' => 4],
        (object) ['name' => 'Elektronik', 'slug' => 'elektronik', 'umkms_count' => 6],
    ]);

    $fallbackProducts = collect([
        (object) [
            'name' => 'Nasi Box Rumahan',
            'price' => 25000,
            'image' => null,
            'category' => (object) ['name' => 'Kuliner'],
            'umkm' => (object) ['name' => 'Dapur Ibu Sari', 'slug' => null, 'whatsapp_url' => null],
            'images' => collect(),
        ],
        (object) [
            'name' => 'Servis Ringan Motor',
            'price' => 45000,
            'image' => null,
            'category' => (object) ['name' => 'Otomotif'],
            'umkm' => (object) ['name' => 'Bengkel Berkah Motor', 'slug' => null, 'whatsapp_url' => null],
            'images' => collect(),
        ],
        (object) [
            'name' => 'Hampers Custom',
            'price' => 85000,
            'image' => null,
            'category' => (object) ['name' => 'Produk Kreatif'],
            'umkm' => (object) ['name' => 'Kriya Cimuning', 'slug' => null, 'whatsapp_url' => null],
            'images' => collect(),
        ],
        (object) [
            'name' => 'Paket Sembako Hemat',
            'price' => 120000,
            'image' => null,
            'category' => (object) ['name' => 'Toko Harian'],
            'umkm' => (object) ['name' => 'Warung Makmur Cimuning', 'slug' => null, 'whatsapp_url' => null],
            'images' => collect(),
        ],
    ]);

    $fallbackFeaturedUmkms = collect([
        (object) [
            'name' => 'Dapur Ibu Sari',
            'category' => (object) ['name' => 'Kuliner'],
            'description' => 'Aneka nasi box, kue basah, dan pesanan harian untuk warga sekitar Cimuning.',
            'rw' => 'RW 03',
            'imageClass' => 'from-cimuning-soft via-white to-orange-50',
            'slug' => null,
            'is_verified' => true,
            'whatsapp_url' => null,
        ],
        (object) [
            'name' => 'Bengkel Berkah Motor',
            'category' => (object) ['name' => 'Otomotif'],
            'description' => 'Servis ringan, ganti oli, dan perawatan motor dengan layanan cepat dan ramah.',
            'rw' => 'RW 06',
            'imageClass' => 'from-blue-50 via-white to-cimuning-section',
            'slug' => null,
            'is_verified' => true,
            'whatsapp_url' => null,
        ],
        (object) [
            'name' => 'Kriya Cimuning',
            'category' => (object) ['name' => 'Produk Kreatif'],
            'description' => 'Kerajinan lokal, souvenir custom, dan hampers untuk acara keluarga maupun komunitas.',
            'rw' => 'RW 01',
            'imageClass' => 'from-green-50 via-white to-cimuning-soft',
            'slug' => null,
            'is_verified' => true,
            'whatsapp_url' => null,
        ],
    ]);

    $categories = ($categories ?? collect())->isNotEmpty() ? $categories : $fallbackCategories;
    $featuredProducts = ($featuredProducts ?? collect())->isNotEmpty() ? $featuredProducts : $fallbackProducts;
    $featuredUmkms = ($featuredUmkms ?? collect())->isNotEmpty() ? $featuredUmkms : $fallbackFeaturedUmkms;

    $banners = [
        [
            'eyebrow' => 'Produk lokal Cimuning',
            'title' => 'Temukan kebutuhan harian dari UMKM sekitar',
            'body' => 'Cari makanan, jasa, toko harian, kerajinan, dan layanan warga dalam satu direktori.',
            'cta' => 'Cari Produk/Jasa',
            'url' => route('products.index'),
            'style' => 'from-cimuning-red via-orange-500 to-amber-400 text-white',
            'accent' => 'bg-white/18 text-white',
        ],
        [
            'eyebrow' => 'Profil verified',
            'title' => 'Buka profil UMKM sebelum menghubungi owner',
            'body' => 'Lihat kategori, alamat, layanan, katalog produk, WhatsApp, dan tautan Google Maps.',
            'cta' => 'Lihat UMKM',
            'url' => route('umkm.index'),
            'style' => 'from-cimuning-blue via-sky-600 to-cyan-400 text-white',
            'accent' => 'bg-white/18 text-white',
        ],
        [
            'eyebrow' => 'Untuk pemilik usaha',
            'title' => 'Kelola profil dan katalog lewat akun owner',
            'body' => 'Daftar akun, lengkapi profil UMKM, tambah produk, lalu tunggu verifikasi admin.',
            'cta' => 'Daftarkan UMKM',
            'url' => route('umkm.register'),
            'style' => 'from-cimuning-charcoal via-slate-700 to-cimuning-deep text-white',
            'accent' => 'bg-white/18 text-white',
        ],
        [
            'eyebrow' => 'Katalog digital',
            'title' => 'Direktori kontak langsung, bukan marketplace checkout',
            'body' => 'Transaksi tetap dilakukan langsung dengan pemilik UMKM melalui WhatsApp atau kunjungan lokasi.',
            'cta' => 'Jelajahi Kategori',
            'url' => route('categories.index'),
            'style' => 'from-emerald-600 via-green-500 to-lime-400 text-white',
            'accent' => 'bg-white/18 text-white',
        ],
    ];

    $imageUrl = function ($product): ?string {
        $imagePath = $product->image ?: $product->images->first()?->path;

        return \App\Support\MediaUrl::get($imagePath);
    };
@endphp

<x-public-layout title="Beranda">
    <section class="bg-cimuning-white py-5 md:py-8">
        <div class="container-cimuning">
            <div
                x-data="{
                    active: 0,
                    total: {{ count($banners) }},
                    timer: null,
                    observer: null,
                    isVisible: false,
                    isPaused: false,
                    scrollTimeout: null,
                    targetLeft(index) {
                        const track = this.$refs.track;
                        const slide = track?.children[index];

                        if (! track || ! slide) {
                            return 0;
                        }

                        return Math.max(slide.offsetLeft - ((track.clientWidth - slide.clientWidth) / 2), 0);
                    },
                    go(index) {
                        this.active = (index + this.total) % this.total;
                        this.$nextTick(() => {
                            this.$refs.track?.scrollTo({
                                left: this.targetLeft(this.active),
                                behavior: 'smooth',
                            });
                        });
                    },
                    next() { this.go(this.active + 1) },
                    prev() { this.go(this.active - 1) },
                    syncFromScroll() {
                        window.clearTimeout(this.scrollTimeout);
                        this.scrollTimeout = window.setTimeout(() => {
                            const track = this.$refs.track;

                            if (! track) {
                                return;
                            }

                            const center = track.scrollLeft + (track.clientWidth / 2);
                            let closest = 0;
                            let closestDistance = Number.POSITIVE_INFINITY;

                            Array.from(track.children).forEach((slide, index) => {
                                const slideCenter = slide.offsetLeft + (slide.clientWidth / 2);
                                const distance = Math.abs(slideCenter - center);

                                if (distance < closestDistance) {
                                    closest = index;
                                    closestDistance = distance;
                                }
                            });

                            this.active = closest;
                        }, 120);
                    },
                    start() {
                        this.stop();

                        if (! this.isVisible || this.isPaused || this.total < 2) {
                            return;
                        }

                        this.timer = window.setInterval(() => this.next(), 6500);
                    },
                    stop() {
                        if (this.timer) {
                            window.clearInterval(this.timer);
                            this.timer = null;
                        }
                    },
                    pause() {
                        this.isPaused = true;
                        this.stop();
                    },
                    resume() {
                        this.isPaused = false;
                        this.start();
                    },
                    initCarousel() {
                        this.$nextTick(() => {
                            this.$refs.track?.scrollTo({ left: this.targetLeft(0), behavior: 'auto' });
                        });

                        if ('IntersectionObserver' in window) {
                            this.observer = new IntersectionObserver(([entry]) => {
                                this.isVisible = entry.isIntersecting;
                                this.isVisible ? this.start() : this.stop();
                            }, { threshold: 0.35 });
                            this.observer.observe(this.$el);

                            return;
                        }

                        this.isVisible = true;
                        this.start();
                    },
                }"
                x-init="initCarousel()"
                x-on:mouseenter="pause()"
                x-on:mouseleave="resume()"
                x-on:focusin="pause()"
                x-on:focusout="resume()"
                data-carousel="home-jumbotron"
                class="relative"
            >
                <div x-ref="track" x-on:scroll.passive="syncFromScroll()" data-carousel-track class="flex snap-x gap-4 overflow-x-auto scroll-smooth pb-2 md:px-12 lg:px-14 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                    @foreach ($banners as $banner)
                        <article class="min-h-[236px] min-w-[88%] snap-center overflow-hidden rounded-card bg-gradient-to-br {{ $banner['style'] }} p-6 shadow-card md:min-w-[68%] md:p-8 lg:min-w-[58%]">
                            <div class="grid h-full gap-6 md:grid-cols-[1.2fr_.8fr] md:items-center">
                                <div>
                                    <span class="inline-flex rounded-button {{ $banner['accent'] }} px-3 py-2 text-xs font-bold uppercase">
                                        {{ $banner['eyebrow'] }}
                                    </span>
                                    <h1 class="mt-5 max-w-xl text-2xl font-bold leading-tight md:text-4xl">
                                        {{ $banner['title'] }}
                                    </h1>
                                    <p class="mt-3 max-w-xl text-base leading-7 text-white/85">
                                        {{ $banner['body'] }}
                                    </p>
                                    <a href="{{ $banner['url'] }}" class="mt-5 inline-flex min-h-11 items-center justify-center rounded-button bg-white px-5 py-3 text-sm font-bold text-cimuning-charcoal transition hover:bg-cimuning-soft">
                                        {{ $banner['cta'] }}
                                    </a>
                                </div>

                                <div class="hidden md:block">
                                    <div class="grid grid-cols-2 gap-3">
                                        <span class="rounded-card bg-white/18 p-5 text-center text-sm font-bold">Katalog Produk</span>
                                        <span class="rounded-card bg-white/18 p-5 text-center text-sm font-bold">Maps</span>
                                        <span class="rounded-card bg-white/18 p-5 text-center text-sm font-bold">WhatsApp</span>
                                        <span class="rounded-card bg-white/18 p-5 text-center text-sm font-bold">Verified</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="pointer-events-none absolute inset-y-0 left-0 right-0 top-0 z-10 hidden items-center justify-between px-3 pb-9 md:flex lg:px-5">
                    <button type="button" x-on:click="prev()" data-carousel-control="prev" class="pointer-events-auto inline-flex h-12 w-12 items-center justify-center rounded-full border border-cimuning-border bg-white/95 text-3xl font-bold leading-none text-cimuning-charcoal shadow-card transition hover:-translate-x-0.5 hover:bg-cimuning-soft focus:outline-2" aria-label="Slide sebelumnya">
                        <span aria-hidden="true" class="-mt-0.5">&lsaquo;</span>
                    </button>
                    <button type="button" x-on:click="next()" data-carousel-control="next" class="pointer-events-auto inline-flex h-12 w-12 items-center justify-center rounded-full border border-cimuning-border bg-white/95 text-3xl font-bold leading-none text-cimuning-charcoal shadow-card transition hover:translate-x-0.5 hover:bg-cimuning-soft focus:outline-2" aria-label="Slide berikutnya">
                        <span aria-hidden="true" class="-mt-0.5">&rsaquo;</span>
                    </button>
                </div>

                <div class="mt-4 flex justify-center gap-2">
                    @foreach ($banners as $banner)
                        <button type="button" x-on:click="go({{ $loop->index }})" data-carousel-dot class="h-2.5 rounded-full transition" x-bind:class="active === {{ $loop->index }} ? 'w-8 bg-cimuning-red' : 'w-2.5 bg-cimuning-border'" aria-label="Buka slide {{ $loop->iteration }}"></button>
                    @endforeach
                </div>
            </div>

            <div class="mt-7 rounded-card border border-cimuning-border bg-white p-4 shadow-card">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-cimuning-charcoal">Kategori</h2>
                    <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-cimuning-blue hover:underline">Lihat Semua</a>
                </div>

                <div class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-6 lg:grid-cols-12">
                    <a href="{{ route('categories.index') }}" class="group flex min-h-[96px] flex-col items-center justify-start gap-2 rounded-card p-2 text-center transition hover:bg-cimuning-section">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cimuning-soft text-cimuning-red transition group-hover:bg-cimuning-red group-hover:text-white">
                            <x-category-icon slug="lihat-semua" class="h-6 w-6" />
                        </span>
                        <span class="text-xs font-semibold leading-4 text-cimuning-charcoal">Lihat Semua</span>
                    </a>

                    @foreach ($categories->take(11) as $category)
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group flex min-h-[96px] flex-col items-center justify-start gap-2 rounded-card p-2 text-center transition hover:bg-cimuning-section">
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-cimuning-soft text-cimuning-red transition group-hover:bg-cimuning-red group-hover:text-white">
                                <x-category-icon :slug="$category->slug" class="h-6 w-6" />
                            </span>
                            <span class="line-clamp-2 text-xs font-semibold leading-4 text-cimuning-charcoal">{{ $category->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-10 md:py-14">
        <div class="container-cimuning">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold uppercase text-cimuning-red">Katalog lokal</p>
                    <h2 class="mt-1 text-2xl font-bold text-cimuning-charcoal md:text-3xl">Produk dan jasa terbaru</h2>
                </div>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold text-cimuning-blue hover:underline">Lihat semua</a>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                @foreach ($featuredProducts as $product)
                    @php
                        $productImageUrl = $imageUrl($product);
                        $umkm = $product->umkm;
                        $detailUrl = $umkm?->slug ? route('umkm.show', $umkm->slug) : route('products.index');
                        $whatsappUrl = $umkm?->whatsapp_url
                            ? $umkm->whatsapp_url.'?text='.urlencode("Halo, saya ingin bertanya tentang {$product->name}.")
                            : null;
                    @endphp
                    <article class="overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card transition hover:-translate-y-0.5 hover:shadow-card-hover">
                        <a href="{{ $detailUrl }}" class="block">
                            <div class="aspect-square bg-gradient-to-br from-cimuning-section via-white to-cimuning-soft">
                                @if ($productImageUrl)
                                    <img src="{{ $productImageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center p-4 text-center">
                                        <span class="rounded-button bg-white/85 px-3 py-2 text-xs font-bold text-cimuning-charcoal shadow-card">{{ $product->category?->name ?? 'Produk' }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-3">
                            <p class="line-clamp-2 min-h-10 text-sm font-bold leading-5 text-cimuning-charcoal">{{ $product->name }}</p>
                            <p class="mt-1 truncate text-xs text-cimuning-slate">{{ $umkm?->name ?? 'UMKM Cimuning' }}</p>
                            <p class="mt-2 text-sm font-bold text-cimuning-red">
                                {{ $product->price ? 'Rp '.number_format($product->price, 0, ',', '.') : 'Hubungi UMKM' }}
                            </p>
                            <div class="mt-3 grid gap-2">
                                <x-secondary-button href="{{ $detailUrl }}" class="min-h-11 w-full px-3 text-xs">Lihat UMKM</x-secondary-button>
                                @if ($whatsappUrl)
                                    <x-whatsapp-button href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="min-h-11 w-full px-3 text-xs">Tanya</x-whatsapp-button>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-cimuning-section py-10 md:py-16">
        <div class="container-cimuning">
            <div class="max-w-2xl">
                <h2 class="text-2xl font-bold text-cimuning-charcoal md:text-3xl">UMKM pilihan</h2>
                <p class="mt-2 text-base leading-7 text-cimuning-slate">Profil usaha verified yang bisa ditemukan melalui katalog digital dan dihubungi langsung oleh warga.</p>
            </div>

            <div class="mt-7 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($featuredUmkms as $umkm)
                    <x-umkm-card
                        :name="$umkm->name"
                        :category="$umkm->category?->name ?? 'UMKM'"
                        :description="$umkm->description"
                        :location="$umkm->rw ?? 'Cimuning'"
                        :image-class="$umkm->imageClass ?? 'from-cimuning-soft to-white'"
                        :verified="$umkm->is_verified ?? true"
                        :slug="$umkm->slug"
                        :whatsapp-url="$umkm->whatsapp_url ? $umkm->whatsapp_url.'?text='.urlencode('Halo, saya melihat profil '.$umkm->name.' di Cimuning Digital Hub.') : null"
                    />
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
                            Buat akun owner untuk mengelola profil usaha, katalog produk, kontak WhatsApp, dan lokasi Maps. Admin akan memverifikasi sebelum tampil publik.
                        </p>
                    </div>
                    <x-primary-button href="{{ route('umkm.register') }}" class="bg-cimuning-red hover:bg-cimuning-deep">Daftarkan UMKM</x-primary-button>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
