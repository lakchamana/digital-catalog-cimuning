<footer role="contentinfo" class="border-t border-cimuning-border bg-white">
    <div class="container-cimuning grid gap-8 py-10 md:grid-cols-[1.35fr_0.9fr_0.9fr_1fr]">
        <div>
            <div class="flex items-center gap-3">
                <img src="{{ asset('assets/brand/logo-cimuning.png') }}" alt="Logo Cimuning Digital Hub" class="h-10 w-10 rounded-xl object-contain">
                <div>
                    <p class="font-bold text-cimuning-charcoal">Cimuning Digital Hub</p>
                    <p class="text-sm text-cimuning-slate">Direktori UMKM Cimuning</p>
                </div>
            </div>
            <p class="mt-4 max-w-md text-base leading-7 text-cimuning-slate">
                Platform direktori UMKM lokal Cimuning yang menghubungkan katalog produk digital, lokasi Google Maps, dan kontak langsung ke pelaku usaha.
            </p>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-cimuning-charcoal">Jelajahi</h2>
            <div class="mt-3 space-y-2 text-sm text-cimuning-slate">
                <a class="block hover:text-cimuning-red" href="{{ route('umkm.index') }}">Direktori UMKM</a>
                <a class="block hover:text-cimuning-red" href="{{ route('products.index') }}">Produk dan Jasa</a>
                <a class="block hover:text-cimuning-red" href="{{ route('categories.index') }}">Semua Kategori</a>
                <a class="block hover:text-cimuning-red" href="{{ route('umkm.register') }}">Daftarkan UMKM</a>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-cimuning-charcoal">Informasi</h2>
            <div class="mt-3 space-y-2 text-sm text-cimuning-slate">
                <a class="block hover:text-cimuning-red" href="{{ route('about') }}">Tentang Kami</a>
                <a class="block hover:text-cimuning-red" href="{{ route('contact') }}">Kontak/Bantuan</a>
                <a class="block hover:text-cimuning-red" href="{{ route('privacy') }}">Kebijakan Privasi</a>
                <a class="block hover:text-cimuning-red" href="{{ route('contact') }}">Bantuan Owner</a>
            </div>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-cimuning-charcoal">Catatan</h2>
            <p class="mt-3 text-sm leading-6 text-cimuning-slate">
                Website ini bukan e-commerce. Transaksi dilakukan langsung antara pembeli dan pemilik UMKM.
            </p>
        </div>
    </div>
</footer>
