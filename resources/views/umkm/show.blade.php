<x-public-layout :title="$umkm->name">
    <section class="bg-cimuning-section">
        <div class="container-cimuning grid gap-8 py-12 lg:grid-cols-[1fr_380px] lg:py-16">
            <div>
                <div class="aspect-[16/9] rounded-card border border-cimuning-border bg-gradient-to-br from-cimuning-soft via-white to-cimuning-section shadow-card"></div>
                <div class="mt-8">
                    <div class="flex flex-wrap items-center gap-2">
                        <x-category-badge>{{ $umkm->category?->name ?? 'UMKM' }}</x-category-badge>
                        @if ($umkm->is_verified)
                            <x-verified-badge />
                        @endif
                    </div>
                    <h1 class="mt-4 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">{{ $umkm->name }}</h1>
                    <p class="mt-4 text-base leading-8 text-cimuning-slate">{{ $umkm->description }}</p>
                </div>
            </div>

            <aside class="h-fit rounded-card border border-cimuning-border bg-white p-5 shadow-card">
                <h2 class="text-xl font-bold text-cimuning-charcoal">Hubungi UMKM</h2>
                <div class="mt-4 space-y-3 text-base text-cimuning-slate">
                    <p><span class="font-semibold text-cimuning-charcoal">Pemilik:</span> {{ $umkm->owner_name ?? '-' }}</p>
                    <p><span class="font-semibold text-cimuning-charcoal">Lokasi:</span> {{ $umkm->rw ?? 'Cimuning' }}</p>
                    <p><span class="font-semibold text-cimuning-charcoal">Alamat:</span> {{ $umkm->address ?? '-' }}</p>
                </div>
                <div class="mt-5 grid gap-3">
                    @if ($umkm->whatsapp_url)
                        <x-whatsapp-button href="{{ $umkm->whatsapp_url }}" class="w-full">Chat WhatsApp</x-whatsapp-button>
                    @endif
                    @if ($umkm->address)
                        <x-secondary-button href="https://www.google.com/maps/search/?api=1&query={{ urlencode($umkm->address) }}" class="w-full">Lihat Lokasi</x-secondary-button>
                    @endif
                </div>
                <p class="mt-4 text-sm leading-6 text-cimuning-slate">Transaksi dilakukan langsung dengan pemilik usaha. Website ini tidak menyediakan checkout atau pembayaran.</p>
            </aside>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning">
            <h2 class="text-2xl font-bold text-cimuning-charcoal">Produk dan jasa</h2>
            @if ($umkm->products->isEmpty())
                <p class="mt-4 text-base text-cimuning-slate">Belum ada produk yang ditampilkan.</p>
            @else
                <div class="mt-7 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($umkm->products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
