<div
    x-data="{
        open: false,
        step: 0,
        query: '',
        storageKey: 'cimuning_walkthrough_seen_v1',
        close() {
            this.open = false;

            try {
                window.localStorage.setItem(this.storageKey, '1');
            } catch (error) {
                window.__cimuningWalkthroughSeen = true;
            }
        },
        start() {
            this.step = 1;
        },
        next() {
            this.step = Math.min(this.step + 1, 3);
        },
        back() {
            this.step = Math.max(this.step - 1, 0);
        },
        search() {
            const term = this.query.trim();

            if (term.length > 0) {
                this.close();
                window.location.href = '{{ route('products.index') }}?search=' + encodeURIComponent(term);
                return;
            }

            this.next();
        },
    }"
    x-init="
        let hasSeen = false;

        try {
            hasSeen = window.localStorage.getItem(storageKey) === '1';
        } catch (error) {
            hasSeen = window.__cimuningWalkthroughSeen === true;
        }

        open = ! hasSeen;
    "
    x-effect="document.body.classList.toggle('overflow-hidden', open)"
    x-on:keydown.escape.window="close()"
    data-onboarding="interactive-walkthrough"
>
    <div
        x-cloak
        x-show="open"
        x-transition.opacity
        class="fixed inset-0 z-[70] bg-cimuning-charcoal/45 backdrop-blur-sm"
        x-on:click="close()"
    ></div>

    <section
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full opacity-0 md:translate-y-4"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0 md:translate-y-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="first-visit-walkthrough-title"
        class="fixed inset-x-0 bottom-0 z-[80] mx-auto max-h-[88dvh] overflow-y-auto rounded-t-[24px] border border-cimuning-border bg-white p-5 shadow-2xl md:bottom-auto md:top-1/2 md:max-w-2xl md:-translate-y-1/2 md:rounded-card md:p-6"
    >
        <div class="flex items-start justify-between gap-4">
            <div>
                <x-category-badge>Interactive Walkthrough</x-category-badge>
                <h2 id="first-visit-walkthrough-title" class="mt-4 text-2xl font-bold leading-tight text-cimuning-charcoal md:text-3xl">
                    Panduan cepat Cimuning Digital Hub
                </h2>
                <p class="mt-3 text-base leading-7 text-cimuning-slate">
                    Ikuti beberapa langkah singkat untuk mencari produk, membuka kategori, menghubungi UMKM, atau mendaftarkan usaha.
                </p>
            </div>

            <button
                type="button"
                x-on:click="close()"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-button border border-cimuning-border text-2xl text-cimuning-slate transition hover:bg-cimuning-section focus:outline-2"
                aria-label="Tutup walkthrough"
            >
                &times;
            </button>
        </div>

        <div class="mt-5 flex items-center gap-2" aria-hidden="true">
            <template x-for="item in [0, 1, 2, 3]" :key="item">
                <span class="h-2 flex-1 rounded-full" x-bind:class="step >= item ? 'bg-cimuning-red' : 'bg-cimuning-border'"></span>
            </template>
        </div>

        <div class="mt-6">
            <div x-show="step === 0">
                <div class="rounded-card border border-cimuning-border bg-cimuning-white p-5">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">Mulai dari pencarian</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Website ini dibuat untuk discovery UMKM: cari kebutuhan, lihat profil, lalu hubungi pemilik usaha langsung. Tidak ada cart, checkout, payment, atau ongkir internal.
                    </p>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <x-primary-button type="button" x-on:click="start()" class="w-full">Mulai Panduan</x-primary-button>
                    <x-secondary-button type="button" x-on:click="close()" class="w-full">Lewati</x-secondary-button>
                </div>
            </div>

            <div x-show="step === 1" x-cloak>
                <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">1. Coba Cari Produk/Jasa</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Ketik kebutuhan seperti nasi box, laundry, servis motor, atau hampers. Search akan membawamu ke halaman produk.
                    </p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_auto]">
                        <label for="walkthrough-search" class="sr-only">Cari Produk/Jasa</label>
                        <input
                            id="walkthrough-search"
                            x-model="query"
                            x-on:keydown.enter.prevent="search()"
                            type="search"
                            placeholder="Contoh: nasi box"
                            class="min-h-11 rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal focus:border-cimuning-red focus:outline-2"
                        >
                        <x-primary-button type="button" x-on:click="search()">Cari Produk/Jasa</x-primary-button>
                    </div>
                </div>
            </div>

            <div x-show="step === 2" x-cloak>
                <div class="rounded-card border border-cimuning-border bg-cimuning-white p-5">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">2. Buka Kategori atau Produk</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Jika belum tahu nama produknya, mulai dari kategori. Setiap kategori mengarah ke produk dan jasa UMKM lokal yang relevan.
                    </p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <x-primary-button href="{{ route('categories.index') }}" x-on:click="close()" class="w-full">Buka Kategori</x-primary-button>
                        <x-secondary-button href="{{ route('products.index') }}" x-on:click="close()" class="w-full">Lihat Produk/Jasa</x-secondary-button>
                    </div>
                </div>
            </div>

            <div x-show="step === 3" x-cloak>
                <div class="rounded-card border border-cimuning-border bg-cimuning-section p-5">
                    <h3 class="text-xl font-bold text-cimuning-charcoal">3. Punya UMKM? Daftarkan Akun Owner</h3>
                    <p class="mt-3 text-base leading-7 text-cimuning-slate">
                        Buat akun owner, lengkapi profil UMKM, tambah produk, lalu tunggu verifikasi admin sebelum tampil publik.
                    </p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <x-primary-button href="{{ route('umkm.register') }}" x-on:click="close()" class="w-full">Daftarkan UMKM</x-primary-button>
                        <x-secondary-button type="button" x-on:click="close()" class="w-full">Selesai</x-secondary-button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="step > 0" x-cloak class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
            <button
                type="button"
                x-on:click="step === 1 ? step = 0 : back()"
                class="inline-flex min-h-11 items-center justify-center rounded-button px-5 py-3 text-sm font-semibold text-cimuning-slate transition hover:bg-cimuning-section focus:outline-2"
            >
                Kembali
            </button>
            <div class="grid gap-3 sm:grid-cols-2">
                <button
                    type="button"
                    x-on:click="close()"
                    class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border px-5 py-3 text-sm font-semibold text-cimuning-charcoal transition hover:bg-cimuning-section focus:outline-2"
                >
                    Skip
                </button>
                <button
                    type="button"
                    x-show="step < 3"
                    x-on:click="next()"
                    class="inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
                >
                    Lanjut
                </button>
            </div>
        </div>
    </section>
</div>
