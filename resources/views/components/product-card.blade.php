@props(['product'])

<article {{ $attributes->merge(['class' => 'overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card transition duration-200 hover:-translate-y-0.5 hover:shadow-card-hover']) }}>
    <div class="aspect-[4/3] bg-gradient-to-br from-cimuning-section via-white to-cimuning-soft">
        <div class="flex h-full items-center justify-center p-6 text-center">
            <span class="rounded-button bg-white/85 px-4 py-2 text-sm font-semibold text-cimuning-charcoal shadow-card">
                {{ $product->category?->name ?? 'Produk/Jasa' }}
            </span>
        </div>
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
            <x-secondary-button href="{{ route('umkm.show', $product->umkm->slug) }}" class="w-full">Lihat UMKM</x-secondary-button>
        @endif
    </div>
</article>
