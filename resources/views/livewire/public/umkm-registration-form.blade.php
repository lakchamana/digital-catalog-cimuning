<section class="bg-white pb-16 pt-10 md:pt-14">
    <div class="container-cimuning">
        @if ($submitted)
            <div class="mx-auto max-w-3xl rounded-card border border-cimuning-border bg-cimuning-section p-6 text-center shadow-card md:p-8">
                <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-cimuning-green text-2xl font-bold text-white">✓</span>
                <h2 class="mt-5 text-2xl font-bold text-cimuning-charcoal">Pendaftaran berhasil dikirim</h2>
                <p class="mt-3 text-base leading-8 text-cimuning-slate">
                    Data {{ $submittedName ? "\"{$submittedName}\"" : 'UMKM Anda' }} sudah masuk dengan status menunggu verifikasi. Admin akan meninjau kelengkapan profil sebelum ditampilkan di direktori publik.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <x-primary-button href="{{ route('umkm.index') }}">Lihat Direktori UMKM</x-primary-button>
                    <x-secondary-button wire:click="createAnother">Daftarkan UMKM Lain</x-secondary-button>
                </div>
            </div>
        @else
            <form wire:submit="submit" class="mx-auto grid max-w-5xl gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-6">
                    <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card md:p-6">
                        <h2 class="text-xl font-bold text-cimuning-charcoal">Profil usaha</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Nama UMKM</span>
                                <input wire:model.blur="name" type="text" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal" placeholder="Contoh: Dapur Ibu Sari">
                                @error('name') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Kategori</span>
                                <select wire:model="category_id" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal">
                                    <option value="">Pilih kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Deskripsi usaha</span>
                                <textarea wire:model.blur="description" rows="5" class="mt-2 w-full rounded-input border border-cimuning-border px-4 py-3 text-base leading-7 text-cimuning-charcoal" placeholder="Ceritakan produk, jasa, keunggulan, dan area layanan UMKM."></textarea>
                                @error('description') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>

                    <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card md:p-6">
                        <h2 class="text-xl font-bold text-cimuning-charcoal">Kontak dan lokasi</h2>
                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Nama penanggung jawab</span>
                                <input wire:model.blur="owner_name" type="text" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal">
                                @error('owner_name') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">WhatsApp aktif</span>
                                <input wire:model.blur="whatsapp" type="tel" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal" placeholder="08xxxxxxxxxx">
                                @error('whatsapp') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Email usaha</span>
                                <input wire:model.blur="email" type="email" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal" placeholder="Opsional">
                                @error('email') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">RW</span>
                                <input wire:model.blur="rw" type="text" class="mt-2 min-h-11 w-full rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal" placeholder="RW 03">
                                @error('rw') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Alamat</span>
                                <textarea wire:model.blur="address" rows="4" class="mt-2 w-full rounded-input border border-cimuning-border px-4 py-3 text-base leading-7 text-cimuning-charcoal" placeholder="Tulis alamat atau patokan lokasi usaha di Cimuning."></textarea>
                                @error('address') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>

                    <div class="rounded-card border border-cimuning-border bg-white p-5 shadow-card md:p-6">
                        <h2 class="text-xl font-bold text-cimuning-charcoal">Layanan dan foto</h2>
                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            @foreach ([
                                'service_delivery' => 'Melayani delivery',
                                'service_cod' => 'Melayani COD',
                                'service_custom_order' => 'Menerima custom order',
                                'has_physical_store' => 'Punya toko fisik',
                            ] as $field => $label)
                                <label class="flex min-h-11 items-center gap-3 rounded-input border border-cimuning-border px-4 text-base text-cimuning-charcoal">
                                    <input wire:model="{{ $field }}" type="checkbox" class="h-5 w-5 rounded border-cimuning-border text-cimuning-red">
                                    <span>{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-5 grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Logo usaha</span>
                                <input wire:model="logo" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-input border border-cimuning-border px-4 py-3 text-base text-cimuning-charcoal">
                                <span class="mt-1 block text-sm text-cimuning-slate">Opsional. JPG, PNG, atau WEBP maksimal 2 MB.</span>
                                @error('logo') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-cimuning-charcoal">Cover usaha</span>
                                <input wire:model="cover" type="file" accept="image/jpeg,image/png,image/webp" class="mt-2 w-full rounded-input border border-cimuning-border px-4 py-3 text-base text-cimuning-charcoal">
                                <span class="mt-1 block text-sm text-cimuning-slate">Opsional. Cocok untuk foto toko, produk, atau aktivitas usaha.</span>
                                @error('cover') <span class="mt-1 block text-sm text-cimuning-red">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </div>
                </div>

                <aside class="h-fit rounded-card border border-cimuning-border bg-cimuning-section p-5 shadow-card lg:sticky lg:top-24">
                    <h2 class="text-xl font-bold text-cimuning-charcoal">Sebelum dikirim</h2>
                    <div class="mt-4 space-y-3 text-base leading-7 text-cimuning-slate">
                        <p>Data akan masuk sebagai <span class="font-semibold text-cimuning-charcoal">menunggu verifikasi</span>.</p>
                        <p>Admin akan memeriksa kategori, kontak, alamat, dan kelengkapan profil sebelum tampil di publik.</p>
                        <p>Website ini hanya direktori; transaksi tetap dilakukan langsung dengan pemilik usaha.</p>
                    </div>
                    <button type="submit" wire:loading.attr="disabled" class="mt-6 inline-flex min-h-11 w-full items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep disabled:cursor-wait disabled:opacity-70">
                        <span wire:loading.remove>Kirim Pendaftaran</span>
                        <span wire:loading>Mengirim...</span>
                    </button>
                </aside>
            </form>
        @endif
    </div>
</section>
