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
        <div class="mb-6 flex flex-col gap-3 rounded-card border border-cimuning-border bg-cimuning-section p-4 md:flex-row md:items-center md:justify-between lg:hidden">
            <p class="text-sm leading-6 text-cimuning-slate">
                Gunakan search utama di navbar untuk mengganti kata kunci.
            </p>
            <div class="flex flex-wrap gap-2">
                <button
                    type="button"
                    x-on:click="openFilters()"
                    x-bind:aria-expanded="filtersOpen.toString()"
                    aria-controls="product-filter-drawer"
                    class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border bg-white px-5 py-3 text-sm font-semibold text-cimuning-charcoal transition hover:bg-white/80 focus:outline-2"
                >
                    {{ $activeFilterCount > 0 ? 'Filter ('.$activeFilterCount.')' : 'Filter' }}
                </button>

                @if ($activeFilterCount > 0)
                    <a
                        href="{{ $resetUrl }}"
                        class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border bg-white px-5 py-3 text-sm font-semibold text-cimuning-charcoal transition hover:bg-white/80 focus:outline-2"
                    >
                        Hapus semua filter
                    </a>
                @endif
            </div>
        </div>

        <div class="grid gap-8 lg:grid-cols-[280px_1fr] lg:items-start">
            <aside class="hidden rounded-card border border-cimuning-border bg-cimuning-section p-5 lg:block" aria-label="Filter produk">
                <div class="mb-5 flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-cimuning-charcoal">Saring hasil</h2>
                        <p class="mt-1 text-sm leading-6 text-cimuning-slate">Filter langsung diterapkan dan URL ikut diperbarui.</p>
                    </div>
                    @if ($activeFilterCount > 0)
                        <a
                            href="{{ $resetUrl }}"
                            class="shrink-0 rounded-button px-2 py-1 text-xs font-semibold text-cimuning-red transition hover:bg-white focus:outline-2"
                        >
                            Hapus
                        </a>
                    @endif
                </div>
                @include('livewire.public.partials.product-filters', ['filterIdSuffix' => 'desktop'])
            </aside>

            <section aria-labelledby="product-results-heading">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 id="product-results-heading" class="text-2xl font-bold text-cimuning-charcoal">{{ $resultHeading }}</h2>
                        <p class="mt-2 text-base text-cimuning-slate" aria-live="polite" aria-atomic="true">Ditemukan {{ $products->total() }} produk/jasa dari UMKM verified.</p>
                        <p class="mt-2 text-sm leading-6 text-cimuning-slate">Gunakan search utama di navbar untuk mengganti kata kunci.</p>
                    </div>
                </div>

                @if ($activeFilterCount > 0)
                    <div class="mt-5 flex flex-wrap gap-2" aria-label="Filter aktif">
                        @foreach ($activeFilters as $filter)
                            <span class="inline-flex min-h-9 items-center gap-2 rounded-full border border-cimuning-border bg-white px-3 py-1.5 text-sm font-semibold text-cimuning-slate shadow-sm">
                                <span class="text-cimuning-muted">{{ $filter['label'] }}:</span>
                                <span class="text-cimuning-charcoal">{{ $filter['value'] }}</span>
                                <a
                                    href="{{ $filter['url'] }}"
                                    class="-mr-1 inline-flex h-6 w-6 items-center justify-center rounded-full text-base leading-none text-cimuning-muted transition hover:bg-cimuning-soft hover:text-cimuning-red focus:outline-2"
                                    aria-label="Hapus filter {{ $filter['label'] }}"
                                >
                                    &times;
                                </a>
                            </span>
                        @endforeach
                    </div>
                @endif

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
                            <div class="mt-5 flex flex-col justify-center gap-3 sm:flex-row">
                                @if ($activeFilterCount > 0)
                                    <a
                                        href="{{ $resetUrl }}"
                                        class="inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
                                    >
                                        Hapus filter
                                    </a>
                                @endif
                                <x-secondary-button href="{{ route('umkm.index') }}">
                                    Lihat direktori UMKM
                                </x-secondary-button>
                            </div>
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

        <div class="mt-5 grid gap-3">
            <button
                type="button"
                x-on:click="closeFilters()"
                class="inline-flex min-h-11 w-full items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
            >
                Lihat hasil
            </button>
            @if ($activeFilterCount > 0)
                <a
                    href="{{ $resetUrl }}"
                    class="inline-flex min-h-11 w-full items-center justify-center rounded-button border border-cimuning-border bg-white px-5 py-3 text-sm font-semibold text-cimuning-charcoal transition hover:bg-cimuning-section focus:outline-2"
                >
                    Hapus semua
                </a>
            @endif
        </div>
    </aside>
</div>
