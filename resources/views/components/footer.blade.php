<footer class="border-t border-cimuning-border bg-white">
    <div class="container-cimuning grid gap-8 py-10 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-cimuning-red text-lg font-bold text-white">C</span>
                <div>
                    <p class="font-bold text-cimuning-charcoal">Cimuning UMKM</p>
                    <p class="text-sm text-cimuning-slate">Online Directory</p>
                </div>
            </div>
            <p class="mt-4 max-w-md text-base leading-7 text-cimuning-slate">
                Platform direktori untuk membantu warga menemukan UMKM Cimuning dan menghubungi pelaku usaha secara langsung.
            </p>
        </div>

        <div>
            <h2 class="text-sm font-semibold text-cimuning-charcoal">Jelajahi</h2>
            <div class="mt-3 space-y-2 text-sm text-cimuning-slate">
                <a class="block hover:text-cimuning-red" href="{{ route('umkm.index') }}">Direktori UMKM</a>
                <a class="block hover:text-cimuning-red" href="{{ route('products.index') }}">Produk dan Jasa</a>
                <a class="block hover:text-cimuning-red" href="{{ route('umkm.register') }}">Daftarkan UMKM</a>
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
