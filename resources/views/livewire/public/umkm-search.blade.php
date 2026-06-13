<div x-data="{ filtersOpen: false }" class="bg-white py-10 md:py-16">
    <div class="container-cimuning">
        <div class="rounded-card border border-cimuning-border bg-white p-4 shadow-card md:p-5">
            <label for="umkm-search" class="text-sm font-semibold text-cimuning-charcoal">Cari UMKM</label>
            <div class="mt-3 grid gap-3 lg:grid-cols-[1fr_auto]">
                <input
                    id="umkm-search"
                    type="search"
                    wire:model.live.debounce.500ms="search"
                    placeholder="Cari produk, jasa, atau nama UMKM..."
                    class="min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:border-cimuning-red focus:outline-2"
                >

                <div class="grid grid-cols-2 gap-3 sm:flex">
                    <button
                        type="button"
                        x-on:click="filtersOpen = true"
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
                Filter tersimpan di URL agar hasil pencarian bisa dibagikan.
            </p>
        </div>

        <div class="mt-8 grid gap-8 lg:grid-cols-[280px_1fr] lg:items-start">
            <aside class="hidden rounded-card border border-cimuning-border bg-cimuning-section p-5 lg:block">
                @include('livewire.public.partials.umkm-filters')
            </aside>

            <section>
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-cimuning-charcoal">Hasil pencarian</h2>
                        <p class="mt-2 text-base text-cimuning-slate">
                            {{ $umkms->total() }} UMKM ditemukan.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if ($category)
                            <x-category-badge>{{ $categories->firstWhere('slug', $category)?->name ?? 'Kategori' }}</x-category-badge>
                        @endif
                        @if ($rw)
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">{{ $rw }}</span>
                        @endif
                        @foreach ($services as $service)
                            <span class="inline-flex items-center rounded-button border border-cimuning-border bg-white px-3 py-1.5 text-xs font-semibold text-cimuning-slate">
                                {{ ['delivery' => 'Delivery', 'cod' => 'COD', 'custom_order' => 'Custom Order', 'physical_store' => 'Toko Fisik'][$service] ?? $service }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div wire:loading.delay class="mt-8 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @for ($i = 0; $i < 6; $i++)
                        <div class="overflow-hidden rounded-card border border-cimuning-border bg-white shadow-card">
                            <div class="aspect-[4/3] animate-pulse bg-cimuning-section"></div>
                            <div class="space-y-4 p-5">
                                <div class="h-5 w-28 animate-pulse rounded-button bg-cimuning-section"></div>
                                <div class="h-6 w-3/4 animate-pulse rounded bg-cimuning-section"></div>
                                <div class="h-16 animate-pulse rounded bg-cimuning-section"></div>
                                <div class="h-11 animate-pulse rounded-button bg-cimuning-section"></div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div wire:loading.delay.remove>
                    @if ($umkms->isEmpty())
                        <div class="mt-8 rounded-card border border-dashed border-cimuning-border bg-cimuning-section p-8 text-center">
                            <h3 class="text-xl font-bold text-cimuning-charcoal">UMKM belum ditemukan.</h3>
                            <p class="mt-2 text-base leading-7 text-cimuning-slate">Coba gunakan kata kunci lain atau pilih kategori berbeda.</p>
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
                            @foreach ($umkms as $umkm)
                                <x-umkm-card
                                    :name="$umkm->name"
                                    :category="$umkm->category?->name ?? 'UMKM'"
                                    :description="$umkm->description"
                                    :location="$umkm->rw ?? 'Cimuning'"
                                    :verified="$umkm->is_verified"
                                    :slug="$umkm->slug"
                                    :whatsapp-url="$umkm->whatsapp_url"
                                    :services="[
                                        'delivery' => $umkm->service_delivery,
                                        'cod' => $umkm->service_cod,
                                        'custom_order' => $umkm->service_custom_order,
                                        'physical_store' => $umkm->has_physical_store,
                                    ]"
                                />
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $umkms->onEachSide(1)->links() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <div x-cloak x-show="filtersOpen" x-transition.opacity class="fixed inset-0 z-50 bg-cimuning-charcoal/40 lg:hidden" x-on:click="filtersOpen = false"></div>
    <aside
        x-cloak
        x-show="filtersOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed inset-x-0 bottom-0 z-50 max-h-[86dvh] overflow-y-auto rounded-t-[24px] bg-white p-5 shadow-2xl lg:hidden"
    >
        <div class="mb-5 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-cimuning-charcoal">Filter UMKM</h2>
                <p class="mt-1 text-sm text-cimuning-slate">Pilih kategori, RW, layanan, dan urutan.</p>
            </div>
            <button type="button" x-on:click="filtersOpen = false" class="flex h-11 w-11 items-center justify-center rounded-button border border-cimuning-border text-2xl text-cimuning-slate" aria-label="Tutup filter">&times;</button>
        </div>

        @include('livewire.public.partials.umkm-filters')

        <button
            type="button"
            x-on:click="filtersOpen = false"
            class="mt-5 inline-flex min-h-11 w-full items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
        >
            Terapkan Filter
        </button>
    </aside>
</div>
