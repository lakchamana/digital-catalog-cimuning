<x-public-layout title="Produk dan Jasa">
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-12 md:py-16">
            <div class="max-w-3xl">
                <x-category-badge>Katalog Produk/Jasa</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Produk dan jasa UMKM Cimuning</h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate">Temukan produk atau layanan dari UMKM verified dan hubungi pemilik usahanya langsung.</p>
            </div>

            <form action="{{ route('products.index') }}" method="GET" class="mt-8 rounded-card border border-cimuning-border bg-white p-4 shadow-card md:p-5">
                <div class="grid gap-3 md:grid-cols-[1fr_auto]">
                    <label class="sr-only" for="search">Cari produk atau jasa</label>
                    <input id="search" name="search" value="{{ $search }}" type="search" placeholder="Cari produk, jasa, atau nama UMKM..." class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:border-cimuning-red focus:outline-2">
                    <x-primary-button class="w-full md:w-auto">Cari Produk</x-primary-button>
                </div>
            </form>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning">
            @if ($products->isEmpty())
                <div class="rounded-card border border-dashed border-cimuning-border bg-cimuning-section p-8 text-center">
                    <h2 class="text-xl font-bold text-cimuning-charcoal">Produk belum ditemukan.</h2>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">Coba kata kunci lain atau jalankan migration dan seeder jika database masih kosong.</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
