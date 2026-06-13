@php
    $links = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'UMKM', 'route' => 'umkm.index'],
        ['label' => 'Produk/Jasa', 'route' => 'products.index'],
        ['label' => 'Tentang', 'route' => 'about'],
        ['label' => 'Kontak', 'route' => 'contact'],
    ];
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-50 border-b border-cimuning-border bg-white/95 backdrop-blur">
    <nav class="container-cimuning flex h-16 items-center justify-between gap-4 lg:h-[72px]" aria-label="Navigasi utama">
        <a href="{{ route('home') }}" class="flex min-h-11 items-center gap-3 rounded-button focus:outline-2">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cimuning-red text-lg font-bold text-white shadow-card">C</span>
            <span class="leading-tight">
                <span class="block text-sm font-bold text-cimuning-charcoal sm:text-base">Cimuning UMKM</span>
                <span class="block text-xs text-cimuning-slate">Online Directory</span>
            </span>
        </a>

        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" @class([
                    'rounded-button px-4 py-2 text-sm font-medium transition hover:text-cimuning-deep focus:outline-2',
                    'text-cimuning-red' => request()->routeIs($link['route']),
                    'text-cimuning-slate' => ! request()->routeIs($link['route']),
                ])>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <div class="hidden items-center gap-3 lg:flex">
            <a href="{{ route('umkm.register') }}" class="inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2">
                Daftarkan UMKM
            </a>
        </div>

        <button type="button" x-on:click="open = true" class="inline-flex h-11 w-11 items-center justify-center rounded-button border border-cimuning-border text-cimuning-charcoal lg:hidden" aria-label="Buka menu navigasi">
            <span class="space-y-1.5">
                <span class="block h-0.5 w-5 rounded bg-current"></span>
                <span class="block h-0.5 w-5 rounded bg-current"></span>
                <span class="block h-0.5 w-5 rounded bg-current"></span>
            </span>
        </button>
    </nav>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-cimuning-charcoal/40 lg:hidden" x-on:click="open = false"></div>
    <aside x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed right-0 top-0 z-50 h-dvh w-[86vw] max-w-sm bg-white shadow-2xl lg:hidden">
        <div class="flex h-16 items-center justify-between border-b border-cimuning-border px-5">
            <span class="font-semibold text-cimuning-charcoal">Menu</span>
            <button type="button" x-on:click="open = false" class="flex h-11 w-11 items-center justify-center rounded-button text-2xl text-cimuning-slate" aria-label="Tutup menu navigasi">&times;</button>
        </div>
        <div class="space-y-2 p-5">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="flex min-h-11 items-center rounded-xl px-3 text-base font-medium text-cimuning-charcoal hover:bg-cimuning-section">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <a href="{{ route('umkm.register') }}" class="mt-4 flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white">
                Daftarkan UMKM
            </a>
        </div>
    </aside>
</header>
