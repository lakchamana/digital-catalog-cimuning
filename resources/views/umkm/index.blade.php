<x-public-layout :title="$pageTitle ?? 'Direktori UMKM'">
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-12 md:py-16">
            <div class="max-w-3xl">
                <x-category-badge>Direktori UMKM</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">
                    {{ $pageTitle ?? 'Temukan UMKM Cimuning' }}
                </h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate">
                    Cari berdasarkan nama usaha, produk, jasa, kategori, deskripsi, atau lokasi RW.
                </p>
            </div>

            <form action="{{ route('umkm.index') }}" method="GET" class="mt-8 rounded-card border border-cimuning-border bg-white p-4 shadow-card md:p-5">
                <div class="grid gap-3 lg:grid-cols-[1fr_240px_auto]">
                    <label class="sr-only" for="search">Keyword</label>
                    <input id="search" name="search" value="{{ $search }}" type="search" placeholder="Cari produk, jasa, atau nama UMKM..." class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:border-cimuning-red focus:outline-2">

                    <label class="sr-only" for="category">Kategori</label>
                    <select id="category" name="category" class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->slug }}" @selected($category === $item->slug || $category === $item->name)>{{ $item->name }}</option>
                        @endforeach
                    </select>

                    <x-primary-button class="w-full lg:w-auto">Cari UMKM</x-primary-button>
                </div>
            </form>
        </div>
    </section>

    <section class="bg-white py-10 md:py-16">
        <div class="container-cimuning">
            <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-cimuning-charcoal">Hasil pencarian</h2>
                    <p class="mt-2 text-base text-cimuning-slate">{{ $umkms->count() }} UMKM ditemukan.</p>
                </div>
                <a href="{{ route('umkm.index') }}" class="text-sm font-semibold text-cimuning-blue hover:underline">Reset filter</a>
            </div>

            @if ($umkms->isEmpty())
                <div class="mt-8 rounded-card border border-dashed border-cimuning-border bg-cimuning-section p-8 text-center">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">UMKM belum ditemukan.</h3>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">Coba gunakan kata kunci lain atau pilih kategori berbeda. Jika database belum dimigrasi, jalankan migration dan seeder lebih dulu.</p>
                </div>
            @else
                <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($umkms as $umkm)
                        <x-umkm-card
                            :name="$umkm->name"
                            :category="$umkm->category?->name ?? 'UMKM'"
                            :description="$umkm->description"
                            :location="$umkm->rw ?? 'Cimuning'"
                            :verified="$umkm->is_verified"
                            :slug="$umkm->slug"
                            :whatsapp-url="$umkm->whatsapp_url"
                        />
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-public-layout>
