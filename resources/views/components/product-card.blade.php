@props(['product'])

@php
    $imagePath = $product->image ?: $product->images->first()?->path;
    $imageUrl = \App\Support\MediaUrl::get($imagePath);
    $galleryCount = $product->relationLoaded('images') ? $product->images->count() : $product->images()->count();
    $detailUrl = $product->slug
        ? route('products.show', $product->slug)
        : ($product->umkm?->slug ? route('umkm.show', $product->umkm->slug) : route('products.index'));
    $whatsappUrl = $product->umkm?->whatsapp_url
        ? $product->umkm->whatsapp_url.'?text='.urlencode("Halo, saya ingin bertanya tentang {$product->name}.")
        : null;
@endphp

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:shadow-card-hover']) }}>
    <a href="{{ $detailUrl }}" class="block focus:outline-2" aria-label="Lihat detail {{ $product->name }}">
        <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-cimuning-section via-white to-cimuning-soft">
            @if ($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
            @else
                <div class="flex h-full items-center justify-center p-6 text-center">
                    <span class="rounded-button bg-white/85 px-4 py-2 text-sm font-semibold text-cimuning-charcoal shadow-card">
                        {{ $product->category?->name ?? $product->umkm?->category?->name ?? 'Produk/Jasa' }}
                    </span>
                </div>
            @endif
            @if ($galleryCount > 1)
                <span class="absolute right-3 top-3 rounded-full bg-white/92 px-3 py-1 text-xs font-bold text-cimuning-charcoal shadow-card">
                    +{{ $galleryCount - 1 }} foto
                </span>
            @endif
        </div>
    </a>
    <div class="space-y-3 p-5">
        <x-category-badge>{{ $product->category?->name ?? $product->umkm?->category?->name ?? 'UMKM' }}</x-category-badge>
        <div>
            <h3 class="text-xl font-bold leading-snug text-cimuning-charcoal">
                <a href="{{ $detailUrl }}" class="rounded-button hover:text-cimuning-red focus:outline-2">{{ $product->name }}</a>
            </h3>
            <p class="mt-1 text-sm font-medium text-cimuning-slate">{{ $product->umkm?->name }}</p>
            <p class="mt-2 line-clamp-3 text-base leading-7 text-cimuning-slate">{{ $product->description }}</p>
        </div>
        <p class="text-lg font-bold text-cimuning-charcoal">
            {{ $product->price ? 'Rp '.number_format($product->price, 0, ',', '.') : 'Harga hubungi UMKM' }}
        </p>
        @if ($product->umkm)
            <div class="grid gap-2 sm:grid-cols-2">
                <x-secondary-button href="{{ $detailUrl }}" class="w-full">Lihat Detail</x-secondary-button>
                @if ($whatsappUrl)
                    <x-whatsapp-button href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="w-full">Tanya Produk</x-whatsapp-button>
                @endif
            </div>
        @endif
    </div>
</article>
