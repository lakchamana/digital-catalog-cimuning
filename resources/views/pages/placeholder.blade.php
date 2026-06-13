<x-public-layout :title="$title">
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-16 md:py-20">
            <div class="max-w-3xl">
                <x-category-badge>Tahap MVP</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">{{ $heading }}</h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate md:text-lg">{{ $description }}</p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <x-primary-button href="{{ route('home') }}">Kembali ke Beranda</x-primary-button>
                    <x-secondary-button href="{{ route('umkm.register') }}">Daftarkan UMKM</x-secondary-button>
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
