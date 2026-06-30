@php
    $ownerLoginUrl = \Illuminate\Support\Facades\Route::has('filament.admin.auth.login')
        ? route('filament.admin.auth.login')
        : route('umkm.register');
    $supportEmail = (string) config('support.email');
    $supportPhone = (string) config('support.whatsapp_display');
    $supportWhatsappUrl = 'https://wa.me/'.preg_replace('/\D+/', '', (string) config('support.whatsapp')).'?text='.
        rawurlencode((string) config('support.whatsapp_message'));
@endphp

<x-public-layout
    title="Kontak & Bantuan"
    description="Pusat bantuan Cimuning Digital Hub untuk mencari produk atau jasa, mendaftarkan UMKM, login owner, verifikasi, revisi data, dan informasi kontak pengelola."
    :canonical="route('contact')"
>
    <section class="bg-cimuning-section">
        <div class="container-cimuning py-12 md:py-16">
            <div class="max-w-3xl">
                <x-category-badge>Bantuan Publik</x-category-badge>
                <h1 class="mt-5 text-3xl font-bold leading-tight text-cimuning-charcoal md:text-5xl">Kontak & Bantuan</h1>
                <p class="mt-5 text-base leading-8 text-cimuning-slate md:text-lg">
                    Temukan panduan penggunaan atau hubungi pengelola melalui email dan WhatsApp untuk bantuan akun, data UMKM, verifikasi, maupun penggunaan direktori.
                </p>
                <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <x-primary-button href="{{ route('umkm.register') }}">Daftarkan UMKM</x-primary-button>
                    <x-secondary-button href="{{ $ownerLoginUrl }}">Masuk Owner</x-secondary-button>
                    <x-secondary-button href="{{ route('products.index') }}">Cari Produk/Jasa</x-secondary-button>
                    <x-secondary-button href="{{ route('umkm.index') }}">Lihat Direktori UMKM</x-secondary-button>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 md:py-16">
        <div class="container-cimuning">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Panduan cepat</p>
                <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal md:text-3xl">Apa yang ingin Anda lakukan?</h2>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['title' => 'Cari produk atau jasa', 'body' => 'Gunakan search utama atau halaman produk untuk menemukan kebutuhan dari UMKM Cimuning.', 'href' => route('products.index'), 'action' => 'Buka Produk/Jasa'],
                    ['title' => 'Lihat profil UMKM', 'body' => 'Buka direktori UMKM untuk melihat kategori, layanan, lokasi, katalog, WhatsApp, dan QR profil.', 'href' => route('umkm.index'), 'action' => 'Buka Direktori'],
                    ['title' => 'Daftarkan usaha', 'body' => 'Calon owner membuat akun, melengkapi profil UMKM, lalu menunggu verifikasi admin.', 'href' => route('umkm.register'), 'action' => 'Daftar Owner'],
                    ['title' => 'Masuk sebagai owner', 'body' => 'Owner yang sudah punya akun bisa mengelola profil UMKM dan produk/jasa dari dashboard.', 'href' => $ownerLoginUrl, 'action' => 'Masuk Dashboard'],
                    ['title' => 'Menunggu verifikasi', 'body' => 'UMKM baru tidak langsung tampil publik. Admin akan memeriksa kelengkapan profil sebelum mengaktifkan direktori.', 'href' => route('umkm.register'), 'action' => 'Lihat Alur Daftar'],
                    ['title' => 'Perlu revisi data', 'body' => 'Jika status perlu revisi, owner dapat memperbaiki profil, kontak, lokasi, layanan, dan foto dari dashboard.', 'href' => $ownerLoginUrl, 'action' => 'Buka Dashboard'],
                    ['title' => 'Kebijakan privasi', 'body' => 'Baca bagaimana data pengunjung, owner, profil UMKM, produk, dan media dikelola di Cimuning Digital Hub.', 'href' => route('privacy'), 'action' => 'Baca Kebijakan'],
                    ['title' => 'Syarat penggunaan', 'body' => 'Baca kewajiban owner, aturan katalog, moderasi, dan ketentuan penggunaan layanan.', 'href' => route('terms'), 'action' => 'Baca Syarat'],
                ] as $item)
                    <article class="flex h-full flex-col rounded-card border border-cimuning-border bg-cimuning-section p-5">
                        <h3 class="text-lg font-bold text-cimuning-charcoal">{{ $item['title'] }}</h3>
                        <p class="mt-3 flex-1 text-sm leading-6 text-cimuning-slate">{{ $item['body'] }}</p>
                        <x-secondary-button href="{{ $item['href'] }}" class="mt-5 w-full">{{ $item['action'] }}</x-secondary-button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-cimuning-section py-12 md:py-16">
        <div class="container-cimuning grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-cimuning-red">Kontak pengelola</p>
                <h2 class="mt-2 text-2xl font-bold text-cimuning-charcoal md:text-3xl">Hubungi bantuan Cimuning Digital Hub</h2>
                <p class="mt-3 text-base leading-8 text-cimuning-slate">
                    Sampaikan kebutuhan secara jelas dan jangan pernah mengirim password, kode login, atau informasi rahasia melalui pesan.
                </p>
                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <a href="mailto:{{ $supportEmail }}" class="inline-flex min-h-11 items-center justify-center rounded-button border border-cimuning-border bg-white px-5 py-3 text-sm font-semibold text-cimuning-charcoal hover:border-cimuning-red hover:text-cimuning-red focus:outline-2">
                        {{ $supportEmail }}
                    </a>
                    <a href="{{ $supportWhatsappUrl }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center justify-center rounded-button bg-cimuning-whatsapp px-5 py-3 text-sm font-semibold text-white hover:brightness-95 focus:outline-2">
                        WhatsApp {{ $supportPhone }}
                    </a>
                </div>
            </div>

            <div class="rounded-card border border-cimuning-border bg-white p-6 shadow-card">
                <h3 class="text-xl font-bold text-cimuning-charcoal">Catatan penting</h3>
                <div class="mt-5 grid gap-4">
                    @foreach ([
                        'Website ini tidak menyediakan checkout, cart, payment, ongkir, atau transaksi internal.',
                        'Komunikasi pembelian dilakukan langsung dengan pemilik UMKM melalui WhatsApp, Maps, website, atau media sosial.',
                        'Data UMKM yang tampil publik adalah data yang sudah aktif dan terverifikasi admin.',
                    ] as $note)
                        <div class="rounded-card border border-cimuning-border bg-cimuning-section p-4 text-sm leading-6 text-cimuning-slate">
                            {{ $note }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-public-layout>
