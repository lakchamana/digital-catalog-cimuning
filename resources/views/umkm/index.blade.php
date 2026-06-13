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

        </div>
    </section>

    <livewire:public.umkm-search :initial-category="$category ?? null" />
</x-public-layout>
