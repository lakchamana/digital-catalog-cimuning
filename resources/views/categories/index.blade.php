@php
    $fallbackCategories = collect([
        (object) ['name' => 'Kuliner', 'slug' => 'kuliner', 'description' => 'Makanan, minuman, kue, katering, dan pesanan rumahan.', 'umkms_count' => 24],
        (object) ['name' => 'Fashion', 'slug' => 'fashion', 'description' => 'Pakaian, hijab, aksesoris, dan produk gaya hidup.', 'umkms_count' => 12],
        (object) ['name' => 'Jasa', 'slug' => 'jasa', 'description' => 'Layanan rumah tangga, servis, desain, dan kebutuhan harian.', 'umkms_count' => 18],
        (object) ['name' => 'Toko Harian', 'slug' => 'toko-harian', 'description' => 'Warung, sembako, perlengkapan harian, dan toko sekitar rumah.', 'umkms_count' => 15],
        (object) ['name' => 'Kecantikan', 'slug' => 'kecantikan', 'description' => 'Salon, skincare, makeup, dan perawatan diri.', 'umkms_count' => 9],
        (object) ['name' => 'Digital', 'slug' => 'digital', 'description' => 'Desain, percetakan, konten, dan jasa berbasis digital.', 'umkms_count' => 7],
        (object) ['name' => 'Otomotif', 'slug' => 'otomotif', 'description' => 'Bengkel, servis kendaraan, dan perlengkapan motor.', 'umkms_count' => 10],
        (object) ['name' => 'Produk Kreatif', 'slug' => 'produk-kreatif', 'description' => 'Kerajinan, souvenir, hampers, dan produk custom.', 'umkms_count' => 8],
        (object) ['name' => 'Pendidikan', 'slug' => 'pendidikan', 'description' => 'Kursus, les, bimbingan belajar, dan layanan edukasi.', 'umkms_count' => 5],
        (object) ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'description' => 'Klinik, terapi, herbal, alat kesehatan, dan layanan kebugaran.', 'umkms_count' => 6],
        (object) ['name' => 'Laundry', 'slug' => 'laundry', 'description' => 'Cuci setrika, dry clean, laundry kiloan, dan layanan kebersihan pakaian.', 'umkms_count' => 4],
        (object) ['name' => 'Elektronik', 'slug' => 'elektronik', 'description' => 'Servis gadget, komputer, perangkat elektronik, dan aksesoris.', 'umkms_count' => 6],
        (object) ['name' => 'Agribisnis', 'slug' => 'agribisnis', 'description' => 'Tanaman, hasil kebun, pangan lokal, dan kebutuhan pertanian rumahan.', 'umkms_count' => 4],
        (object) ['name' => 'Properti/Rumah', 'slug' => 'properti-rumah', 'description' => 'Kontrakan, renovasi, perbaikan rumah, dan kebutuhan properti.', 'umkms_count' => 3],
        (object) ['name' => 'Event & Catering', 'slug' => 'event-catering', 'description' => 'Katering, dekorasi, dokumentasi, dan kebutuhan acara.', 'umkms_count' => 7],
        (object) ['name' => 'Anak & Bayi', 'slug' => 'anak-bayi', 'description' => 'Perlengkapan anak, mainan, dan kebutuhan bayi.', 'umkms_count' => 5],
    ]);

    $categories = ($categories ?? collect())->isNotEmpty() ? $categories : $fallbackCategories;
@endphp

<x-public-layout title="Semua Kategori">
    <section class="bg-cimuning-white py-8 md:py-12">
        <div class="container-cimuning">
            <div class="max-w-3xl">
                <x-category-badge>Kategori UMKM</x-category-badge>
                <h1 class="mt-4 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">
                    Jelajahi semua kategori Cimuning
                </h1>
                <p class="mt-4 text-base leading-8 text-cimuning-slate md:text-lg">
                    Pilih kategori untuk menemukan produk, jasa, dan profil UMKM lokal yang sudah diverifikasi di Cimuning Digital Hub.
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white py-8 md:py-12">
        <div class="container-cimuning">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('products.index', ['category' => $category->slug]) }}"
                        class="group rounded-card border border-cimuning-border bg-white p-5 shadow-card transition hover:-translate-y-0.5 hover:border-cimuning-red hover:shadow-card-hover"
                    >
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-cimuning-soft text-cimuning-red transition group-hover:bg-cimuning-red group-hover:text-white">
                            <x-category-icon :slug="$category->slug" class="h-7 w-7" />
                        </span>
                        <span class="mt-4 block text-lg font-bold text-cimuning-charcoal">{{ $category->name }}</span>
                        <span class="mt-2 line-clamp-3 block min-h-[4.5rem] text-sm leading-6 text-cimuning-slate">
                            {{ $category->description ?: 'Kategori produk dan jasa lokal Cimuning.' }}
                        </span>
                        <span class="mt-4 inline-flex min-h-11 items-center rounded-button border border-cimuning-border px-4 text-sm font-semibold text-cimuning-charcoal transition group-hover:border-cimuning-red group-hover:text-cimuning-red">
                            {{ $category->umkms_count ?? 0 }} UMKM
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
</x-public-layout>
