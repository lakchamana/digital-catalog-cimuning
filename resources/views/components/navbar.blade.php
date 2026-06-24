@php
    $primaryLinks = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Produk/Jasa', 'route' => 'products.index'],
        ['label' => 'UMKM', 'route' => 'umkm.index'],
        ['label' => 'Kategori', 'route' => 'categories.index'],
    ];

    $secondaryLinks = [
        ['label' => 'Tentang Kami', 'route' => 'about'],
        ['label' => 'Kontak', 'route' => 'contact'],
        ['label' => 'Privasi', 'route' => 'privacy'],
        ['label' => 'Bantuan', 'route' => 'contact'],
    ];

    $ownerLoginUrl = \Illuminate\Support\Facades\Route::has('filament.admin.auth.login')
        ? route('filament.admin.auth.login')
        : route('umkm.register');
    $ownerRegisterUrl = \Illuminate\Support\Facades\Route::has('filament.admin.auth.register')
        ? route('filament.admin.auth.register')
        : route('umkm.register');
    $isUmkmSearchContext = request()->routeIs('umkm.index') || request()->routeIs('categories.show');
    $navbarSearchRoute = $isUmkmSearchContext ? route('umkm.index') : route('products.index');
    $navbarSearchLabel = $isUmkmSearchContext
        ? 'Cari nama UMKM, kategori, atau RW Cimuning'
        : 'Cari produk, jasa, atau UMKM Cimuning';
    $navbarSearchPlaceholder = $isUmkmSearchContext
        ? 'Cari nama UMKM, kategori, atau RW...'
        : 'Cari produk, jasa, atau UMKM Cimuning...';
    $mobileSearchPlaceholder = $isUmkmSearchContext
        ? 'Cari UMKM atau RW...'
        : 'Cari produk atau jasa...';
@endphp

<div
    x-data="{
        open: false,
        openMenu() {
            this.open = true;
            this.$nextTick(() => this.$refs.mobileMenuTitle?.focus());
        },
        closeMenu(restoreFocus = true) {
            const wasOpen = this.open;
            this.open = false;
            if (wasOpen && restoreFocus) {
                this.$nextTick(() => this.$refs.mobileMenuButton?.focus());
            }
        },
    }"
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    x-on:keydown.escape.window="closeMenu()"
    x-on:resize.window="if (window.innerWidth >= 1024 && open) closeMenu(false)"
    data-mobile-navigation
>
    <header class="sticky top-0 z-40 border-b border-cimuning-border bg-white/95 backdrop-blur">
    <div class="hidden border-b border-cimuning-border bg-cimuning-section/70 lg:block">
        <div class="container-cimuning flex min-h-9 items-center justify-between gap-4 text-xs font-medium text-cimuning-slate">
            <span>Direktori UMKM Cimuning berbasis katalog produk dan kontak langsung</span>
            <nav class="flex items-center gap-1" aria-label="Navigasi sekunder">
                @foreach ($secondaryLinks as $link)
                    <a href="{{ route($link['route']) }}" class="rounded-button px-3 py-2 transition hover:text-cimuning-red focus:outline-2">
                        {{ $link['label'] }}
                    </a>
                @endforeach
                <span class="mx-1 h-4 w-px bg-cimuning-border"></span>
                <a href="{{ $ownerLoginUrl }}" class="rounded-button px-3 py-2 font-semibold text-cimuning-charcoal transition hover:text-cimuning-red focus:outline-2">
                    Masuk Owner
                </a>
                <a href="{{ $ownerRegisterUrl }}" class="rounded-button px-3 py-2 font-semibold text-cimuning-red transition hover:text-cimuning-deep focus:outline-2">
                    Daftar Owner
                </a>
            </nav>
        </div>
    </div>

    <div class="container-cimuning py-3 lg:py-4">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex min-h-11 shrink-0 items-center gap-3 rounded-button focus:outline-2">
                <img src="{{ asset('assets/brand/logo-cimuning.png') }}" alt="Logo Cimuning Digital Hub" class="h-10 w-10 rounded-xl object-contain shadow-card lg:h-11 lg:w-11">
                <span class="hidden leading-tight sm:block">
                    <span class="block text-sm font-bold text-cimuning-charcoal lg:text-base">Cimuning Digital Hub</span>
                    <span class="block text-xs text-cimuning-slate">Direktori UMKM Cimuning</span>
                </span>
            </a>

            <form action="{{ $navbarSearchRoute }}" method="GET" class="hidden min-w-0 flex-1 lg:block" role="search" aria-label="{{ $navbarSearchLabel }}">
                <div class="flex min-h-12 overflow-hidden rounded-button border border-cimuning-border bg-white shadow-card transition focus-within:border-cimuning-red focus-within:outline-2">
                    <label for="navbar-search" class="sr-only">{{ $navbarSearchLabel }}</label>
                    <input
                        id="navbar-search"
                        name="search"
                        type="search"
                        value="{{ request('search') }}"
                        placeholder="{{ $navbarSearchPlaceholder }}"
                        class="min-w-0 flex-1 border-0 bg-white px-5 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:outline-none"
                    >
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center gap-2 bg-cimuning-red px-6 text-sm font-bold text-white transition hover:bg-cimuning-deep focus:outline-2">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m20 20-4.5-4.5M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        Cari
                    </button>
                </div>
            </form>

            <a href="{{ route('umkm.register') }}" class="hidden min-h-12 shrink-0 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2 lg:inline-flex">
                Daftarkan UMKM
            </a>

            <button
                type="button"
                x-ref="mobileMenuButton"
                x-on:click="openMenu()"
                x-bind:aria-expanded="open.toString()"
                aria-controls="mobile-navigation-drawer"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-button border border-cimuning-border text-cimuning-charcoal focus:outline-2 lg:hidden"
                aria-label="Buka menu navigasi"
            >
                <span class="space-y-1.5">
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                    <span class="block h-0.5 w-5 rounded bg-current"></span>
                </span>
            </button>
        </div>

        <form action="{{ $navbarSearchRoute }}" method="GET" class="mt-3 lg:hidden" role="search" aria-label="{{ $navbarSearchLabel }}">
            <div class="flex min-h-11 overflow-hidden rounded-button border border-cimuning-border bg-white shadow-card">
                <label for="mobile-navbar-search" class="sr-only">{{ $navbarSearchLabel }}</label>
                <input
                    id="mobile-navbar-search"
                    name="search"
                    type="search"
                    value="{{ request('search') }}"
                    placeholder="{{ $mobileSearchPlaceholder }}"
                    class="min-w-0 flex-1 border-0 bg-white px-4 text-base text-cimuning-charcoal placeholder:text-cimuning-muted focus:outline-none"
                >
                <button type="submit" class="inline-flex min-h-11 w-12 items-center justify-center bg-cimuning-red text-white focus:outline-2" aria-label="Cari">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="m20 20-4.5-4.5M18 11a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </button>
            </div>
        </form>

        <nav class="mt-3 hidden items-center gap-2 lg:flex" aria-label="Navigasi discovery">
            @foreach ($primaryLinks as $link)
                <a href="{{ route($link['route']) }}" @class([
                    'rounded-button px-4 py-2 text-sm font-semibold transition',
                    'bg-cimuning-soft text-cimuning-red' => request()->routeIs($link['route']),
                    'text-cimuning-slate hover:bg-cimuning-section hover:text-cimuning-charcoal' => ! request()->routeIs($link['route']),
                ]) @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>
    </div>

    </header>

    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[60] bg-cimuning-charcoal/40 lg:hidden"
        data-mobile-navigation-backdrop
        x-on:click="closeMenu()"
        aria-hidden="true"
    ></div>
    <aside
        id="mobile-navigation-drawer"
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        role="dialog"
        aria-modal="true"
        aria-labelledby="mobile-menu-title"
        class="fixed inset-y-0 right-0 z-[70] h-dvh w-[min(86vw,22rem)] overflow-y-auto overscroll-contain bg-white shadow-2xl lg:hidden"
    >
        <div class="flex h-16 items-center justify-between border-b border-cimuning-border px-5">
            <h2 id="mobile-menu-title" x-ref="mobileMenuTitle" tabindex="-1" class="font-semibold text-cimuning-charcoal">Menu</h2>
            <button type="button" x-on:click="closeMenu()" class="flex h-11 w-11 items-center justify-center rounded-button text-2xl text-cimuning-slate focus:outline-2" aria-label="Tutup menu navigasi">&times;</button>
        </div>
        <div class="space-y-6 p-5">
            <div>
                <p class="text-xs font-semibold uppercase text-cimuning-muted">Jelajahi</p>
                <div class="mt-2 space-y-1">
                    @foreach ($primaryLinks as $link)
                        <a href="{{ route($link['route']) }}" x-on:click="closeMenu(false)" @class([
                            'flex min-h-11 items-center rounded-xl px-3 text-base font-medium focus:outline-2',
                            'bg-cimuning-soft text-cimuning-red' => request()->routeIs($link['route']),
                            'text-cimuning-charcoal hover:bg-cimuning-section' => ! request()->routeIs($link['route']),
                        ]) @if (request()->routeIs($link['route'])) aria-current="page" @endif>
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div>
                <p class="text-xs font-semibold uppercase text-cimuning-muted">Informasi</p>
                <div class="mt-2 space-y-1">
                    @foreach ($secondaryLinks as $link)
                        <a href="{{ route($link['route']) }}" x-on:click="closeMenu(false)" class="flex min-h-11 items-center rounded-xl px-3 text-base font-medium text-cimuning-charcoal hover:bg-cimuning-section focus:outline-2">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-3">
                <a href="{{ route('umkm.register') }}" x-on:click="closeMenu(false)" class="flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white focus:outline-2">
                    Daftarkan UMKM
                </a>
                <a href="{{ $ownerLoginUrl }}" x-on:click="closeMenu(false)" class="flex min-h-11 items-center justify-center rounded-button border border-cimuning-border px-5 py-3 text-sm font-semibold text-cimuning-charcoal focus:outline-2">
                    Masuk Owner
                </a>
            </div>
        </div>
    </aside>
</div>
