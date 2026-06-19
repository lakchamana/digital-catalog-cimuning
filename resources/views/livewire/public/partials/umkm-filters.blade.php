@php
    $filterIdSuffix = $filterIdSuffix ?? 'default';
@endphp

<div class="space-y-6">
    <div>
        <label for="category-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Kategori</label>
        <select id="category-filter-{{ $filterIdSuffix }}" wire:model.live="category" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua kategori</option>
            @foreach ($categories as $item)
                <option value="{{ $item->slug }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="rw-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Lokasi/RW</label>
        <select id="rw-filter-{{ $filterIdSuffix }}" wire:model.live="rw" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua RW</option>
            @foreach ($rws as $item)
                <option value="{{ $item }}">{{ $item }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="sort-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Urutkan</label>
        <select id="sort-filter-{{ $filterIdSuffix }}" wire:model.live="sort" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="latest">Terbaru</option>
            <option value="az">A-Z</option>
        </select>
    </div>

    <div>
        <label for="per-page-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Jumlah per halaman</label>
        <select id="per-page-filter-{{ $filterIdSuffix }}" wire:model.live="perPage" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="9">9 UMKM</option>
            <option value="18">18 UMKM</option>
            <option value="27">27 UMKM</option>
        </select>
    </div>

    <div>
        <p class="text-sm font-semibold text-cimuning-charcoal">Status</p>
        <label class="mt-3 flex min-h-11 items-center gap-3 rounded-xl border border-cimuning-border bg-white px-3 text-base text-cimuning-charcoal">
            <input type="checkbox" wire:model.live="verified" class="h-5 w-5 rounded border-cimuning-border text-cimuning-green focus:ring-cimuning-red">
            Tampilkan UMKM verified
        </label>
        <p class="mt-2 text-sm leading-6 text-cimuning-slate">Untuk keamanan public directory, UMKM pending/rejected tetap tidak ditampilkan.</p>
    </div>

    <div>
        <p class="text-sm font-semibold text-cimuning-charcoal">Layanan</p>
        <div class="mt-3 space-y-2">
            @foreach ([
                'delivery' => 'Bisa delivery',
                'cod' => 'Bisa COD',
                'custom_order' => 'Custom order',
                'physical_store' => 'Toko fisik',
            ] as $value => $label)
                <label class="flex min-h-11 items-center gap-3 rounded-xl border border-cimuning-border bg-white px-3 text-base text-cimuning-charcoal">
                    <input type="checkbox" wire:model.live="services" value="{{ $value }}" class="h-5 w-5 rounded border-cimuning-border text-cimuning-red focus:ring-cimuning-red">
                    {{ $label }}
                </label>
            @endforeach
        </div>
    </div>
</div>
