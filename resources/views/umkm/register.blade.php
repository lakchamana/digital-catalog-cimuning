<x-public-layout title="Daftarkan UMKM">
    <section class="bg-cimuning-section">
        <div class="container-cimuning grid gap-8 py-12 lg:grid-cols-[1fr_360px] lg:items-center lg:py-16">
            <div>
                <x-category-badge>Pendaftaran UMKM</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Kelola profil UMKM lewat akun owner</h1>
                <p class="mt-5 max-w-2xl text-base leading-8 text-cimuning-slate md:text-lg">
                    Buat akun owner, lengkapi profil usaha, lalu admin akan meninjau sebelum UMKM tampil di direktori publik Cimuning Digital Hub.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <x-primary-button href="{{ route('filament.admin.auth.register') }}">Buat Akun Owner</x-primary-button>
                    <x-secondary-button href="{{ route('filament.admin.auth.login') }}">Sudah punya akun? Masuk</x-secondary-button>
                </div>
                <p class="mt-4 text-sm leading-6 text-cimuning-slate">
                    Setelah diverifikasi, profil usaha, produk/jasa, WhatsApp, dan lokasi Maps bisa ditemukan warga. Website ini tetap tidak menyediakan checkout atau pembayaran.
                </p>
            </div>

            <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card">
                <h2 class="text-xl font-bold text-cimuning-charcoal">Alur pendaftaran</h2>
                <div class="mt-5 space-y-3">
                    @foreach ([
                        ['title' => 'Buat akun owner', 'body' => 'Gunakan nama, email aktif, dan password untuk masuk dashboard.'],
                        ['title' => 'Lengkapi profil UMKM', 'body' => 'Isi kategori, alamat, kontak WhatsApp, layanan, logo, dan cover usaha.'],
                        ['title' => 'Tunggu verifikasi admin', 'body' => 'UMKM baru berstatus pending dan belum tampil publik sampai disetujui.'],
                    ] as $step)
                        <div class="flex gap-3 rounded-card border border-cimuning-border bg-cimuning-white p-4">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-button bg-cimuning-soft text-sm font-bold text-cimuning-deep">{{ $loop->iteration }}</span>
                            <div>
                                <h3 class="font-bold text-cimuning-charcoal">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-cimuning-slate">{{ $step['body'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-10 md:py-14">
        <div class="container-cimuning grid gap-5 md:grid-cols-3">
            @foreach ([
                ['title' => 'Profil bisa direvisi', 'body' => 'Owner dapat masuk kembali untuk memperbarui informasi usaha dan katalog produk.'],
                ['title' => 'Status lebih jelas', 'body' => 'Dashboard menampilkan status pending, verified, rejected, atau perlu revisi.'],
                ['title' => 'Kontak tetap langsung', 'body' => 'Warga menghubungi UMKM lewat WhatsApp atau Maps, tanpa transaksi internal.'],
            ] as $item)
                <article class="rounded-card border border-cimuning-border bg-cimuning-white p-5 shadow-card">
                    <h2 class="text-lg font-bold text-cimuning-charcoal">{{ $item['title'] }}</h2>
                    <p class="mt-2 text-base leading-7 text-cimuning-slate">{{ $item['body'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-public-layout>
