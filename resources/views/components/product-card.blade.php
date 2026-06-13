@props(['product'])

@php
    $imagePath = $product->image ?: $product->images->first()?->path;
    $imageUrl = $imagePath ? (\Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://']) ? $imagePath : asset('storage/'.$imagePath)) : null;
    $whatsappUrl = $product->umkm?->whatsapp_url && $product->umkm?->slug
        ? route('leads.redirect', [
            'umkm' => $product->umkm->slug,
            'type' => 'whatsapp',
            'product' => $product->id,
            'source' => 'product_card',
        ])
        : null;
@endphp

<article {{ $attributes->merge(['class' => 'group overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:shadow-card-hover']) }}>
    <div class="relative aspect-[4/3] overflow-hidden bg-gradient-to-br from-cimuning-section via-white to-cimuning-soft">
        @if ($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center p-6 text-center">
                <span class="rounded-button bg-white/85 px-4 py-2 text-sm font-semibold text-cimuning-charcoal shadow-card">
                    {{ $product->category?->name ?? 'Produk/Jasa' }}
                </span>
            </div>
        @endif
    </div>
    <div class="space-y-3 p-5">
        <x-category-badge>{{ $product->category?->name ?? 'UMKM' }}</x-category-badge>
        <div>
            <h3 class="text-xl font-bold leading-snug text-cimuning-charcoal">{{ $product->name }}</h3>
            <p class="mt-1 text-sm font-medium text-cimuning-slate">{{ $product->umkm?->name }}</p>
            <p class="mt-2 line-clamp-3 text-base leading-7 text-cimuning-slate">{{ $product->description }}</p>
        </div>
        <p class="text-lg font-bold text-cimuning-charcoal">
            {{ $product->price ? 'Rp '.number_format($product->price, 0, ',', '.') : 'Harga hubungi UMKM' }}
        </p>
        @if ($product->umkm)
            <div class="grid gap-2 sm:grid-cols-2">
                <x-secondary-button href="{{ route('umkm.show', $product->umkm->slug) }}" class="w-full">Lihat UMKM</x-secondary-button>
                @if ($whatsappUrl)
                    <x-whatsapp-button href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="w-full">Tanya Produk</x-whatsapp-button>
                @endif
            </div>
        @endif
    </div>
</article>
