@php
    $filterIdSuffix = $filterIdSuffix ?? 'default';
@endphp

<div class="space-y-6">
    <div>
        <label for="product-category-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Kategori</label>
        <select id="product-category-filter-{{ $filterIdSuffix }}" wire:model.live="category" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua kategori</option>
            @foreach ($categories as $item)
                <option value="{{ $item->slug }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="product-umkm-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">UMKM</label>
        <select id="product-umkm-filter-{{ $filterIdSuffix }}" wire:model.live="umkm" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua UMKM</option>
            @foreach ($umkms as $item)
                <option value="{{ $item->slug }}">{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="product-price-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Harga</label>
        <select id="product-price-filter-{{ $filterIdSuffix }}" wire:model.live="price" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="all">Semua produk</option>
            <option value="priced">Ada harga</option>
            <option value="contact">Hubungi UMKM</option>
        </select>
    </div>

    <div>
        <label for="product-sort-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Urutkan</label>
        <select id="product-sort-filter-{{ $filterIdSuffix }}" wire:model.live="sort" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="latest">Terbaru</option>
            <option value="az">A-Z</option>
            <option value="price_low">Harga terendah</option>
            <option value="price_high">Harga tertinggi</option>
        </select>
    </div>

    <div>
        <label for="product-per-page-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Jumlah per halaman</label>
        <select id="product-per-page-filter-{{ $filterIdSuffix }}" wire:model.live="perPage" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="9">9 produk</option>
            <option value="18">18 produk</option>
            <option value="27">27 produk</option>
        </select>
    </div>
</div>
