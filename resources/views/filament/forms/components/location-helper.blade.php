<div
    x-data="{
        message: '',
        findInput(name) {
            return document.querySelector(`input[name='data.${name}'], input[name='data[${name}]'], input[id$='${name}']`);
        },
        setInput(name, value) {
            const input = this.findInput(name);

            if (! input) {
                return;
            }

            input.value = value;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        },
        parseCoordinates(text) {
            const patterns = [
                /@(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/,
                /[?&](?:q|query)=(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/,
                /(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/,
            ];

            for (const pattern of patterns) {
                const match = text.match(pattern);

                if (! match) {
                    continue;
                }

                return { latitude: match[1], longitude: match[2] };
            }

            return null;
        },
        useCurrentLocation() {
            if (! navigator.geolocation) {
                this.message = 'Browser tidak mendukung fitur lokasi. Anda tetap bisa menempel koordinat dari Google Maps.';
                return;
            }

            this.message = 'Meminta izin lokasi...';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.setInput('latitude', position.coords.latitude.toFixed(7));
                    this.setInput('longitude', position.coords.longitude.toFixed(7));
                    this.message = 'Lokasi berhasil diisi. Silakan cek kembali titiknya sebelum menyimpan.';
                },
                () => {
                    this.message = 'Izin lokasi ditolak atau gagal. Anda bisa menempel link Google Maps atau mengisi koordinat manual.';
                },
                { enableHighAccuracy: true, timeout: 10000 },
            );
        },
        fillFromMapsText() {
            const mapsInput = this.findInput('maps_link');
            const coordinates = this.parseCoordinates(mapsInput?.value || '');

            if (! coordinates) {
                this.message = 'Koordinat belum ditemukan. Buka Google Maps, pilih lokasi, lalu salin link/alamat lengkap dari browser.';
                return;
            }

            this.setInput('latitude', coordinates.latitude);
            this.setInput('longitude', coordinates.longitude);
            this.message = 'Koordinat dari Google Maps berhasil diisi.';
        },
        openMaps() {
            const addressInput = document.querySelector(`textarea[name='data.address'], textarea[name='data[address]'], textarea[id$='address']`);
            const query = encodeURIComponent(addressInput?.value || 'Cimuning Mustikajaya Bekasi');
            window.open(`https://www.google.com/maps/search/?api=1&query=${query}`, '_blank', 'noopener');
        },
    }"
    class="rounded-xl border border-gray-200 bg-gray-50 p-4"
>
    <p class="text-sm font-medium text-gray-950">Bantu isi titik Google Maps</p>
    <p class="mt-1 text-sm leading-6 text-gray-600">
        Tidak perlu API berbayar. Anda bisa memakai lokasi perangkat, menempel koordinat/link Maps, atau membuka Google Maps untuk mencari titik usaha.
    </p>
    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
        <button type="button" x-on:click="useCurrentLocation()" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500">
            Gunakan lokasi saya
        </button>
        <button type="button" x-on:click="fillFromMapsText()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Ambil dari link Maps
        </button>
        <button type="button" x-on:click="openMaps()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Buka Google Maps
        </button>
    </div>
    <p x-show="message" x-text="message" class="mt-3 text-sm leading-6 text-gray-600"></p>
</div>
