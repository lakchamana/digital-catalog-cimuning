<div
    x-data="{
        filtersOpen: false,
        openFilters() {
            this.filtersOpen = true;
            this.$nextTick(() => this.$refs.filterTitle?.focus());
        },
        closeFilters() {
            this.filtersOpen = false;
        },
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', filtersOpen)"
    x-on:keydown.escape.window="closeFilters()"
    class="bg-white py-10 md:py-16"
>
    <div class="container-cimuning">
        <div class="rounded-card border border-cimuning-border bg-white p-4 shadow-card md:p-5">
            <label for="product-search" class="text-sm font-semibold text-cimuning-charcoal">Cari produk atau jasa</label>
            <div class="mt-3 grid gap-3 lg:grid-cols-[1fr_auto]">
                <input
                    id="product-search"
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari produk, jasa, atau nama UMKM..."
                    class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:border-cimuning-red focus:outline-2"
                >

                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <button
                        type="button"
                        x-on:click="openFilters()"
                        x-bind:aria-expanded="filtersOpen.toString()"
                        aria-controls="product-filter-drawer"
                        class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border bg-white px-5 py-3 text-sm font-semibold text-cimuning-charcoal transition hover:bg-cimuning-section focus:outline-2 lg:hidden"
                    >
                        Filter
                        @if ($activeFilterCount > 0)
                            <span class="ml-2 rounded-button bg-cimuning-red px-2 py-0.5 text-xs text-white">{{ $activeFilterCount }}</span>
                        @endif
                    </button>

                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-red bg-white px-5 py-3 text-sm font-semibold text-cimuning-red transition hover:bg-cimuning-soft focus:outline-2"
                    >
                        Reset
                    </button>
                </div>
            </div>
            <p class="mt-3 text-sm leading-6 text-cimuning-slate">
                Filter produk tersimpan di URL agar katalog bisa dibagikan.
            </p>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[280px_1fr] lg:items-start">
            <aside class="hidden rounded-card border border-cimuning-border bg-cimuning-section p-5 lg:block" aria-label="Filter produk">
                @include('livewire.public.partials.product-filters', ['filterIdSuffix' => 'desktop'])
            </aside>

            <section aria-labelledby="product-results-heading">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 id="product-results-heading" class="text-2xl font-bold text-cimuning-charcoal">Hasil katalog</h2>
                        <p class="mt-2 text-base text-cimuning-slate" aria-live="polite" aria-atomic="true">{{ $products->total() }} produk atau jasa ditemukan.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($category)
                            <x-category-badge>{{ $categories->firstWhere('slug', $category)?->name ?? 'Kategori' }}</x-category-badge>
                        @endif
                        @if ($umkm)
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">{{ $umkms->firstWhere('slug', $umkm)?->name ?? 'UMKM' }}</span>
                        @endif
                        @if ($price !== 'all')
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">{{ $price === 'priced' ? 'Ada harga' : 'Hubungi UMKM' }}</span>
                        @endif
                        @if ($sort !== 'latest')
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">{{ $sort === 'az' ? 'A-Z' : ($sort === 'price_low' ? 'Harga terendah' : 'Harga tertinggi') }}</span>
                        @endif
                        @if ((int) $perPage !== 9)
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">{{ $perPage }} per halaman</span>
                        @endif
                    </div>
                </div>

                <div wire:loading.delay class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3" role="status" aria-label="Memuat hasil produk">
                    <span class="sr-only">Memuat hasil produk atau jasa.</span>
                    @for ($i = 0; $i < 6; $i++)
                        <div class="overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card">
                            <div class="aspect-[4/3] animate-pulse bg-cimuning-section"></div>
                            <div class="space-y-4 p-5">
                                <div class="h-5 w-24 animate-pulse rounded-button bg-cimuning-section"></div>
                                <div class="h-6 w-3/4 animate-pulse rounded bg-cimuning-section"></div>
                                <div class="h-16 animate-pulse rounded bg-cimuning-section"></div>
                                <div class="h-11 animate-pulse rounded-button bg-cimuning-section"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div wire:loading.delay.remove aria-live="polite">
                    @if ($products->isEmpty())
                        <div class="mt-8 rounded-card border border-dashed border-cimuning-border bg-cimuning-section p-8 text-center">
                            <h3 class="text-xl font-bold text-cimuning-charcoal">Produk belum ditemukan.</h3>
                            <p class="mt-2 text-base leading-7 text-cimuning-slate">Coba gunakan kata kunci lain, pilih kategori berbeda, atau buka direktori UMKM.</p>
                            <button
                                type="button"
                                wire:click="resetFilters"
                                class="mt-5 inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
                            >
                                Reset pencarian
                            </button>
                        </div>
                    @else
                        <div class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $products->onEachSide(1)->links() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div x-cloak x-show="filtersOpen" x-transition.opacity class="fixed inset-0 z-50 bg-cimuning-charcoal/40 lg:hidden" x-on:click="closeFilters()"></div>
    <aside
        id="product-filter-drawer"
        x-cloak
        x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="product-filter-title"
        class="fixed inset-x-0 bottom-0 z-50 max-h-[86dvh] overflow-y-auto rounded-t-[24px] bg-white p-5 shadow-2xl lg:hidden"
    >
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 id="product-filter-title" x-ref="filterTitle" tabindex="-1" class="text-xl font-bold text-cimuning-charcoal">Filter Produk</h2>
                <p class="mt-1 text-sm text-cimuning-slate">Pilih kategori, UMKM, harga, dan urutan.</p>
            </div>
            <button type="button" x-on:click="closeFilters()" class="flex h-11 w-11 items-center justify-center rounded-button border border-cimuning-border text-2xl text-cimuning-slate focus:outline-2" aria-label="Tutup filter">&times;</button>
        </div>

        @include('livewire.public.partials.product-filters', ['filterIdSuffix' => 'mobile'])

        <button
            type="button"
            x-on:click="closeFilters()"
            class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
        >
            Terapkan Filter
        </button>
    </aside>
</div>
