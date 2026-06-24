@php
    $filterIdSuffix = $filterIdSuffix ?? 'default';
@endphp

@once
    <script>
        window.CimuningFilters = window.CimuningFilters || {
            submit(form) {
                const params = new URLSearchParams(new FormData(form));
                const defaults = JSON.parse(form.dataset.defaults || '{}');

                for (const [key, value] of [...params.entries()]) {
                    if (value === '' || String(defaults[key] ?? '') === value) {
                        params.delete(key);
                    }
                }

                const query = params.toString();
                window.location.href = form.action + (query ? `?${query}` : '');
            },
        };
    </script>
@endonce

<form
    method="GET"
    action="{{ route('products.index') }}"
    data-defaults='{"category":"","umkm":"","price":"all","sort":"latest","perPage":"9"}'
    onchange="window.CimuningFilters.submit(this)"
    class="space-y-6"
>
    @if ($search !== '')
        <input type="hidden" name="search" value="{{ $search }}">
    @endif

    <div>
        <label for="product-category-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Kategori</label>
        <select id="product-category-filter-{{ $filterIdSuffix }}" name="category" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua kategori</option>
            @foreach ($categories as $item)
                <option value="{{ $item->slug }}" @selected($category === $item->slug)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="product-umkm-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">UMKM</label>
        <select id="product-umkm-filter-{{ $filterIdSuffix }}" name="umkm" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="">Semua UMKM</option>
            @foreach ($umkms as $item)
                <option value="{{ $item->slug }}" @selected($umkm === $item->slug)>{{ $item->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="product-price-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Harga</label>
        <select id="product-price-filter-{{ $filterIdSuffix }}" name="price" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="all" @selected($price === 'all')>Semua produk</option>
            <option value="priced" @selected($price === 'priced')>Ada harga</option>
            <option value="contact" @selected($price === 'contact')>Hubungi UMKM</option>
        </select>
    </div>

    <div>
        <label for="product-sort-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Urutkan</label>
        <select id="product-sort-filter-{{ $filterIdSuffix }}" name="sort" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="latest" @selected($sort === 'latest')>Terbaru</option>
            <option value="az" @selected($sort === 'az')>A-Z</option>
            <option value="price_low" @selected($sort === 'price_low')>Harga terendah</option>
            <option value="price_high" @selected($sort === 'price_high')>Harga tertinggi</option>
        </select>
    </div>

    <div>
        <label for="product-per-page-filter-{{ $filterIdSuffix }}" class="text-sm font-semibold text-cimuning-charcoal">Jumlah per halaman</label>
        <select id="product-per-page-filter-{{ $filterIdSuffix }}" name="perPage" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border bg-white px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2">
            <option value="9" @selected((int) $perPage === 9)>9 produk</option>
            <option value="18" @selected((int) $perPage === 18)>18 produk</option>
            <option value="27" @selected((int) $perPage === 27)>27 produk</option>
        </select>
    </div>
</form>
