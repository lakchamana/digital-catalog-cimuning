@php
    $umkm = $product->umkm;
    $category = $product->category ?: $umkm?->category;
    $mainImageUrl = \App\Support\MediaUrl::get($product->image);
    $galleryItems = collect();

    if ($mainImageUrl) {
        $galleryItems->push([
            'url' => $mainImageUrl,
            'alt' => 'Foto '.$product->name,
        ]);
    }

    foreach ($product->images as $image) {
        $url = \App\Support\MediaUrl::get($image->path);

        if ($url && ! $galleryItems->contains('url', $url)) {
            $galleryItems->push([
                'url' => $url,
                'alt' => $image->alt_text ?: 'Foto '.$product->name,
            ]);
        }
    }

    $socialImage = $galleryItems->first()['url'] ?? asset('assets/brand/logo-cimuning.png');
    $priceLabel = $product->price ? 'Rp '.number_format($product->price, 0, ',', '.') : 'Harga hubungi UMKM';
    $whatsappUrl = $umkm?->whatsapp_url
        ? $umkm->whatsapp_url.'?text='.urlencode("Halo, saya ingin bertanya tentang {$product->name}.")
        : null;
    $profileUrl = $umkm ? route('umkm.show', $umkm->slug) : route('products.index');
    $hasCoordinates = $umkm && filled($umkm->latitude) && filled($umkm->longitude);
    $mapQuery = $hasCoordinates
        ? "{$umkm->latitude},{$umkm->longitude}"
        : ($umkm?->address ?: null);
    $mapsUrl = $mapQuery ? 'https://www.google.com/maps/search/?api=1&query='.urlencode($mapQuery) : null;
    $seoDescription = \Illuminate\Support\Str::limit(
        trim(($product->description ?: "{$product->name} dari {$umkm?->name}.").' Lihat detail produk, UMKM pemilik, WhatsApp, dan profil usaha di Cimuning Digital Hub.'),
        155,
    );
    $structuredData = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => $product->description,
        'image' => $galleryItems->pluck('url')->values()->all() ?: [$socialImage],
        'category' => $category?->name,
        'brand' => $umkm ? [
            '@type' => 'Brand',
            'name' => $umkm->name,
        ] : null,
        'offers' => $product->price ? [
            '@type' => 'Offer',
            'price' => $product->price,
            'priceCurrency' => 'IDR',
            'availability' => 'https://schema.org/InStock',
            'url' => route('products.show', $product->slug),
            'seller' => $umkm ? [
                '@type' => 'LocalBusiness',
                'name' => $umkm->name,
                'url' => route('umkm.show', $umkm->slug),
            ] : null,
        ] : null,
    ], fn ($value) => filled($value) || is_array($value));
@endphp

<x-public-layout
    :title="$product->name"
    :description="$seoDescription"
    :canonical="route('products.show', $product->slug)"
    :image="$socialImage"
    type="product"
    :structured-data="$structuredData"
>
    <section class="bg-cimuning-section pb-28 pt-8 md:py-12">
        <div class="container-cimuning">
            <nav class="mb-5 text-sm font-medium text-cimuning-slate" aria-label="Breadcrumb">
                <a href="{{ route('products.index') }}" class="hover:text-cimuning-red">Produk/Jasa</a>
                <span class="mx-2 text-cimuning-muted">/</span>
                <span class="text-cimuning-charcoal">{{ $product->name }}</span>
            </nav>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px] lg:items-start">
                <div class="min-w-0">
                    <div
                        class="overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card"
                        x-data="{ activeImage: 0 }"
                    >
                        @if ($galleryItems->isNotEmpty())
                            <div class="relative aspect-[4/3] bg-cimuning-section">
                                @foreach ($galleryItems as $index => $item)
                                    <img
                                        x-show="activeImage === {{ $index }}"
                                        src="{{ $item['url'] }}"
                                        alt="{{ $item['alt'] }}"
                                        class="h-full w-full object-cover"
                                        @if ($index > 0) x-cloak @endif
                                    >
                                @endforeach
                            </div>

                            @if ($galleryItems->count() > 1)
                                <div class="grid grid-cols-4 gap-2 border-t border-cimuning-border p-3 sm:grid-cols-6">
                                    @foreach ($galleryItems as $index => $item)
                                        <button
                                            type="button"
                                            x-on:click="activeImage = {{ $index }}"
                                            class="aspect-square overflow-hidden rounded-card border bg-white focus:outline-2"
                                            x-bind:class="activeImage === {{ $index }} ? 'border-cimuning-red ring-2 ring-cimuning-soft' : 'border-cimuning-border'"
                                            aria-label="Tampilkan foto {{ $index + 1 }} {{ $product->name }}"
                                        >
                                            <img src="{{ $item['url'] }}" alt="" class="h-full w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <div class="flex aspect-[4/3] items-center justify-center bg-gradient-to-br from-cimuning-soft via-white to-cimuning-section p-8 text-center">
                                <div>
                                    <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Produk/Jasa</p>
                                    <p class="mt-2 text-2xl font-bold text-cimuning-charcoal">{{ $category?->name ?? 'Katalog UMKM' }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 rounded-card border border-cimuning-border bg-white p-5 shadow-card">
                        <div class="flex flex-wrap items-center gap-2">
                            <x-category-badge>{{ $category?->name ?? 'Produk/Jasa' }}</x-category-badge>
                            @if ($umkm?->is_verified)
                                <x-verified-badge />
                            @endif
                        </div>
                        <h1 class="mt-4 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">{{ $product->name }}</h1>
                        <p class="mt-4 text-2xl font-bold text-cimuning-red">{{ $priceLabel }}</p>
                        <div class="mt-5 border-t border-cimuning-border pt-5">
                            <h2 class="text-xl font-bold text-cimuning-charcoal">Deskripsi produk</h2>
                            <p class="mt-3 whitespace-pre-line text-base leading-8 text-cimuning-slate">
                                {{ $product->description ?: 'Pemilik UMKM belum menambahkan deskripsi lengkap untuk produk atau jasa ini.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <aside class="h-fit rounded-card border border-cimuning-border bg-white p-5 shadow-card lg:sticky lg:top-24">
                    <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">UMKM Pemilik</p>
                    <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal">{{ $umkm?->name ?? 'UMKM Cimuning' }}</h2>
                    <div class="mt-4 space-y-3 text-base leading-7 text-cimuning-slate">
                        <p><span class="font-semibold text-cimuning-charcoal">Kategori:</span> {{ $umkm?->category?->name ?? $category?->name ?? '-' }}</p>
                        <p><span class="font-semibold text-cimuning-charcoal">RW:</span> {{ $umkm?->rw ?? 'Cimuning' }}</p>
                        <p><span class="font-semibold text-cimuning-charcoal">Alamat tertulis:</span> {{ $umkm?->address ?? 'Alamat belum dilengkapi.' }}</p>
                    </div>

                    <div class="mt-5 grid gap-3">
                        @if ($whatsappUrl)
                            <x-whatsapp-button :href="$whatsappUrl" target="_blank" rel="noopener" class="w-full">Tanya Produk</x-whatsapp-button>
                        @endif
                        <x-secondary-button :href="$profileUrl" class="w-full">Lihat Profil UMKM</x-secondary-button>
                        @if ($mapsUrl)
                            <x-secondary-button :href="$mapsUrl" target="_blank" rel="noopener" class="w-full">Buka Maps</x-secondary-button>
                        @endif
                    </div>

                    <p class="mt-4 text-sm leading-6 text-cimuning-slate">
                        Website ini hanya menampilkan katalog dan kontak langsung. Pembelian, pembayaran, atau janji temu dilakukan langsung dengan pemilik UMKM.
                    </p>
                </aside>
            </div>
        </div>
    </section>

    @if ($whatsappUrl || $umkm)
        <div class="fixed inset-x-0 bottom-0 z-40 border-t border-cimuning-border bg-white/95 px-4 py-3 shadow-[0_-8px_24px_rgba(16,24,40,0.12)] backdrop-blur lg:hidden">
            <div class="mx-auto grid max-w-xl gap-2 {{ $whatsappUrl ? 'grid-cols-2' : 'grid-cols-1' }}">
                @if ($whatsappUrl)
                    <x-whatsapp-button :href="$whatsappUrl" target="_blank" rel="noopener" class="w-full px-3">Tanya Produk</x-whatsapp-button>
                @endif
                <x-secondary-button :href="$profileUrl" class="w-full px-3">Profil UMKM</x-secondary-button>
            </div>
        </div>
    @endif
</x-public-layout>
