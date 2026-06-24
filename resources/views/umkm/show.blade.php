@php
    $coverUrl = \App\Support\MediaUrl::get($umkm->cover_image);
    $logoUrl = \App\Support\MediaUrl::get($umkm->logo_image);
    $socialImage = $coverUrl ?: ($logoUrl ?: asset('assets/brand/logo-cimuning.png'));
    $hasCoordinates = filled($umkm->latitude) && filled($umkm->longitude);
    $mapQuery = $hasCoordinates
        ? "{$umkm->latitude},{$umkm->longitude}"
        : ($umkm->address ?: null);
    $mapsUrl = $mapQuery ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapQuery) : null;
    $mapsEmbedUrl = $mapQuery ? 'https://www.google.com/maps?q='.urlencode($mapQuery).'&output=embed' : null;
    $whatsappUrl = $umkm->whatsapp_url
        ? $umkm->whatsapp_url.'?text='.urlencode("Halo, saya melihat profil {$umkm->name} di Cimuning Digital Hub.")
        : null;
    $qrSvgUrl = route('qr.umkm.svg', $umkm->slug);
    $qrDownloadUrl = $qrSvgUrl.'?download=1';
    $profileUrl = route('umkm.show', $umkm->slug);
    $services = [
        'Delivery' => $umkm->service_delivery,
        'COD' => $umkm->service_cod,
        'Custom order' => $umkm->service_custom_order,
        'Toko fisik' => $umkm->has_physical_store,
    ];
    $locationText = collect([$umkm->rw, $umkm->address])->filter()->implode(', ') ?: 'Cimuning';
    $seoDescription = \Illuminate\Support\Str::limit(
        trim("{$umkm->name} adalah UMKM ".($umkm->category?->name ?? 'lokal')." di {$locationText}. ".($umkm->description ?: 'Lihat profil, katalog produk, WhatsApp, dan lokasi Maps di Cimuning Digital Hub.')),
        155,
    );
    $structuredData = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => $umkm->name,
        'description' => $seoDescription,
        'url' => route('umkm.show', $umkm->slug),
        'image' => $socialImage,
        'logo' => $logoUrl ?: asset('assets/brand/logo-cimuning.png'),
        'telephone' => $umkm->phone ?: $umkm->whatsapp,
        'email' => $umkm->email,
        'address' => array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $umkm->address,
            'addressLocality' => 'Cimuning',
            'addressRegion' => 'Kota Bekasi',
            'addressCountry' => 'ID',
        ]),
        'areaServed' => 'Cimuning, Mustikajaya, Kota Bekasi',
        'knowsAbout' => $umkm->category?->name,
        'sameAs' => collect([$umkm->website, $umkm->instagram, $umkm->tiktok])->filter()->values()->all(),
        'makesOffer' => $umkm->products->map(fn ($product) => array_filter([
            '@type' => 'Offer',
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'priceCurrency' => $product->price ? 'IDR' : null,
            'availability' => 'https://schema.org/InStock',
            'url' => route('umkm.show', $umkm->slug),
        ]))->values()->all(),
    ];

    if ($hasCoordinates) {
        $structuredData['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => (float) $umkm->latitude,
            'longitude' => (float) $umkm->longitude,
        ];
    }

    $structuredData = array_filter($structuredData, fn ($value) => filled($value) || is_array($value));
@endphp

<x-public-layout
    :title="$umkm->name"
    :description="$seoDescription"
    :canonical="route('umkm.show', $umkm->slug)"
    :image="$socialImage"
    type="business.business"
    :structured-data="$structuredData"
>
    <section class="bg-cimuning-section">
        <div class="container-cimuning grid gap-8 pb-10 pt-8 lg:grid-cols-[1fr_380px] lg:pb-16 lg:pt-12">
            <div class="min-w-0">
                <div class="relative overflow-hidden rounded-card border border-cimuning-border bg-gradient-to-br from-cimuning-soft via-white to-cimuning-section shadow-card">
                    <div class="aspect-[16/9]">
                        @if ($coverUrl)
                            <img src="{{ $coverUrl }}" alt="Cover {{ $umkm->name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full items-center justify-center p-6 text-center">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Cimuning Digital Hub</p>
                                    <p class="mt-2 text-2xl font-bold text-cimuning-charcoal">{{ $umkm->category?->name ?? 'UMKM Lokal' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-end">
                    <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo {{ $umkm->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-cimuning-red">{{ \Illuminate\Support\Str::of($umkm->name)->substr(0, 1)->upper() }}</span>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-category-badge>{{ $umkm->category?->name ?? 'UMKM' }}</x-category-badge>
                            @if ($umkm->is_verified)
                                <x-verified-badge />
                            @endif
                            @if ($umkm->rw)
                                <span class="rounded-button border border-cimuning-border bg-white px-3 py-1 text-sm font-semibold text-cimuning-slate">{{ $umkm->rw }}</span>
                            @endif
                        </div>
                        <h1 class="mt-4 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">{{ $umkm->name }}</h1>
                        <p class="mt-4 max-w-3xl text-base leading-8 text-cimuning-slate">{{ $umkm->description ?: 'Profil UMKM ini sedang dilengkapi oleh pengelola.' }}</p>
                    </div>
                </div>

                @if (collect($services)->filter()->isNotEmpty())
                    <div class="mt-6 flex flex-wrap gap-2">
                        @foreach ($services as $label => $enabled)
                            @if ($enabled)
                                <span class="rounded-button border border-cimuning-border bg-white px-4 py-2 text-sm font-semibold text-cimuning-charcoal shadow-card">{{ $label }}</span>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            <aside class="h-fit rounded-card border border-cimuning-border bg-white p-5 shadow-card lg:sticky lg:top-24">
                <h2 class="text-xl font-bold text-cimuning-charcoal">Hubungi UMKM</h2>
                <div class="mt-4 space-y-3 text-base leading-7 text-cimuning-slate">
                    <p><span class="font-semibold text-cimuning-charcoal">Pemilik:</span> {{ $umkm->owner_name ?? '-' }}</p>
                    <p><span class="font-semibold text-cimuning-charcoal">WhatsApp:</span> {{ $umkm->whatsapp ?? '-' }}</p>
                    <p><span class="font-semibold text-cimuning-charcoal">Lokasi:</span> {{ $umkm->rw ?? 'Cimuning' }}</p>
                    <p><span class="font-semibold text-cimuning-charcoal">Alamat tertulis:</span> {{ $umkm->address ?? 'Alamat belum dilengkapi.' }}</p>
                    @if ($hasCoordinates)
                        <p><span class="font-semibold text-cimuning-charcoal">Titik Maps:</span> sudah tersedia</p>
                    @endif
                </div>
                <div class="mt-5 grid gap-3">
                    @if ($whatsappUrl)
                        <x-whatsapp-button :href="$whatsappUrl" target="_blank" rel="noopener" class="w-full">Chat WhatsApp</x-whatsapp-button>
                    @endif
                    @if ($mapsUrl)
                        <x-secondary-button :href="$mapsUrl" target="_blank" rel="noopener" class="w-full">Buka Maps</x-secondary-button>
                    @endif
                </div>
                <p class="mt-4 text-sm leading-6 text-cimuning-slate">Transaksi dilakukan langsung dengan pemilik usaha. Website ini tidak menyediakan checkout atau pembayaran.</p>

                <div class="mt-6 rounded-card border border-cimuning-border bg-cimuning-section p-4">
                    <h2 class="text-lg font-bold text-cimuning-charcoal">Bagikan Profil UMKM</h2>
                    <p class="mt-2 text-sm leading-6 text-cimuning-slate">Scan QR ini untuk membuka profil, katalog produk, lokasi, dan kontak {{ $umkm->name }}.</p>
                    <a href="{{ $profileUrl }}" class="mt-4 block rounded-card border border-cimuning-border bg-white p-3 shadow-card" aria-label="Buka profil {{ $umkm->name }}">
                        <img src="{{ $qrSvgUrl }}" alt="QR profil {{ $umkm->name }}" class="mx-auto h-48 w-48">
                    </a>
                    <x-secondary-button :href="$qrDownloadUrl" class="mt-4 w-full" download>Download QR</x-secondary-button>
                </div>
            </aside>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning grid gap-6 lg:grid-cols-[1fr_380px]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Lokasi</p>
                <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal">Temukan lokasi usaha</h2>
                <p class="mt-3 max-w-2xl text-base leading-8 text-cimuning-slate">
                    Gunakan titik Maps bila tersedia, lalu cocokkan dengan alamat tertulis. Pastikan kembali alamat atau titik temu melalui WhatsApp sebelum berkunjung.
                </p>
            </div>

            <div class="rounded-card border border-cimuning-border bg-cimuning-section p-5">
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Alamat tertulis</p>
                <p class="mt-2 text-base font-semibold text-cimuning-charcoal">{{ $umkm->address ?? 'Alamat belum tersedia' }}</p>
                @if ($hasCoordinates)
                    <p class="mt-4 text-sm font-semibold uppercase tracking-wide text-cimuning-red">Titik Google Maps</p>
                    <p class="mt-2 text-sm text-cimuning-slate">{{ $umkm->latitude }}, {{ $umkm->longitude }}</p>
                @else
                    <p class="mt-4 text-sm leading-6 text-cimuning-slate">Titik Google Maps belum dilengkapi. Tombol Maps akan mencari berdasarkan alamat tertulis jika tersedia.</p>
                @endif
                @if ($mapsUrl)
                    <x-secondary-button :href="$mapsUrl" target="_blank" rel="noopener" class="mt-4 w-full">Buka di Google Maps</x-secondary-button>
                @endif
            </div>
        </div>

        <div class="container-cimuning mt-7">
            @if ($mapsEmbedUrl)
                <div class="overflow-hidden rounded-card border border-cimuning-border bg-cimuning-section shadow-card">
                    <iframe
                        src="{{ $mapsEmbedUrl }}"
                        title="Peta lokasi {{ $umkm->name }}"
                        class="h-72 w-full border-0 md:h-96"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            @else
                <div class="rounded-card border border-dashed border-cimuning-border bg-cimuning-section p-8 text-center">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">Lokasi belum dilengkapi</h3>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">Hubungi pemilik usaha untuk mendapatkan alamat atau titik temu yang paling akurat.</p>
                </div>
            @endif
        </div>
    </section>

    <section class="bg-cimuning-section pb-28 pt-10 md:pb-16 md:pt-16">
        <div class="container-cimuning">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Katalog digital</p>
                    <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal">Produk dan jasa</h2>
                </div>
                <p class="max-w-xl text-base leading-7 text-cimuning-slate">Pilih produk atau jasa yang menarik, lalu tanyakan langsung ke pemilik usaha melalui WhatsApp.</p>
            </div>

            @if ($umkm->products->isEmpty())
                <div class="mt-7 rounded-card border border-dashed border-cimuning-border bg-white p-8 text-center">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">Belum ada produk yang ditampilkan</h3>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">Katalog UMKM ini akan diperbarui setelah pemilik usaha menambahkan produk atau jasa.</p>
                </div>
            @else
                <div class="mt-7 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($umkm->products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($whatsappUrl || $mapsUrl)
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-cimuning-border bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(16,24,40,0.12)] backdrop-blur lg:hidden">
            <div class="mx-auto grid max-w-xl gap-2 {{ $whatsappUrl && $mapsUrl ? 'grid-cols-2' : 'grid-cols-1' }}">
                @if ($whatsappUrl)
                    <x-whatsapp-button :href="$whatsappUrl" target="_blank" rel="noopener" class="w-full px-3">Chat WhatsApp</x-whatsapp-button>
                @endif
                @if ($mapsUrl)
                    <x-secondary-button :href="$mapsUrl" target="_blank" rel="noopener" class="w-full px-3">Buka Maps</x-secondary-button>
                @endif
            </div>
        </div>
    @endif
</x-public-layout>
