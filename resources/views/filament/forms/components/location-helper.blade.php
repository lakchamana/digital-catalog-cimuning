<div
    x-data="{
        message: '',
        savedPointUrl: '',
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
            try {
                text = decodeURIComponent(text || '');
            } catch (error) {
                text = text || '';
            }

            const patterns = [
                { regex: /@(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/, lat: 1, lng: 2 },
                { regex: /[?&](?:q|query|ll|center)=(-?\d{1,2}\.\d+),\s*(-?\d{1,3}\.\d+)/, lat: 1, lng: 2 },
                { regex: /!3d(-?\d{1,2}\.\d+)!4d(-?\d{1,3}\.\d+)/, lat: 1, lng: 2 },
                { regex: /!4d(-?\d{1,3}\.\d+)!3d(-?\d{1,2}\.\d+)/, lat: 2, lng: 1 },
                { regex: /(-?\d{1,2}\.\d+)\s*,\s*(-?\d{1,3}\.\d+)/, lat: 1, lng: 2 },
            ];

            for (const pattern of patterns) {
                const match = text.match(pattern.regex);

                if (! match) {
                    continue;
                }

                const latitude = Number.parseFloat(match[pattern.lat]);
                const longitude = Number.parseFloat(match[pattern.lng]);

                if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
                    continue;
                }

                return { latitude: latitude.toFixed(7), longitude: longitude.toFixed(7) };
            }

            return null;
        },
        updateSavedPoint() {
            const latitude = this.findInput('latitude')?.value;
            const longitude = this.findInput('longitude')?.value;

            this.savedPointUrl = latitude && longitude
                ? `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`
                : '';
        },
        setCoordinates(latitude, longitude, shouldUpdateMapsLink = false) {
            this.setInput('latitude', latitude);
            this.setInput('longitude', longitude);

            const url = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;

            if (shouldUpdateMapsLink) {
                this.setInput('maps_link', url);
            }

            this.savedPointUrl = url;
        },
        useCurrentLocation() {
            if (! navigator.geolocation) {
                this.message = 'Browser tidak mendukung fitur lokasi. Anda tetap bisa menempel koordinat dari Google Maps.';
                return;
            }

            this.message = 'Meminta izin lokasi perangkat. Gunakan fitur ini saat Anda sedang berada di lokasi usaha.';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude.toFixed(7);
                    const longitude = position.coords.longitude.toFixed(7);

                    this.setCoordinates(latitude, longitude, true);
                    this.message = 'Titik Maps berhasil diambil dari perangkat. Cek titiknya, lalu pastikan alamat lengkap tetap ditulis manual.';
                },
                () => {
                    this.message = 'Izin lokasi ditolak atau gagal. Anda bisa menempel URL Google Maps lengkap atau koordinat lokasi usaha.';
                },
                { enableHighAccuracy: true, timeout: 10000 },
            );
        },
        fillFromMapsText() {
            const mapsInput = this.findInput('maps_link');
            const value = mapsInput?.value || '';
            const coordinates = this.parseCoordinates(value);

            if (! coordinates) {
                this.message = value.includes('maps.app.goo.gl') || value.includes('goo.gl/maps')
                    ? 'Link pendek belum berisi koordinat. Buka link sampai Google Maps terbuka, lalu salin URL lengkap dari address bar atau tempel koordinat.'
                    : 'Koordinat belum ditemukan. Tempel URL Maps yang berisi titik koordinat atau gunakan tombol lokasi saat berada di tempat usaha.';
                return;
            }

            this.setCoordinates(coordinates.latitude, coordinates.longitude);
            this.message = 'Titik Maps sudah terbaca. Silakan cek titik tersimpan sebelum menyimpan.';
        },
        openSavedPoint() {
            this.updateSavedPoint();

            if (! this.savedPointUrl) {
                this.message = 'Titik Maps belum tersedia. Ambil lokasi perangkat atau tempel URL Maps lengkap terlebih dahulu.';
                return;
            }

            window.open(this.savedPointUrl, '_blank', 'noopener');
        },
        openMaps() {
            const addressInput = document.querySelector(`textarea[name='data.address'], textarea[name='data[address]'], textarea[id$='address']`);
            const query = encodeURIComponent(addressInput?.value || 'Cimuning Mustikajaya Bekasi');
            window.open(`https://www.google.com/maps/search/?api=1&query=${query}`, '_blank', 'noopener');
        },
    }"
    x-init="updateSavedPoint()"
    class="rounded-xl border border-gray-200 bg-gray-50 p-4"
>
    <p class="text-sm font-medium text-gray-950">Bantu isi titik Google Maps</p>
    <p class="mt-1 text-sm leading-6 text-gray-600">
        Titik Maps dan alamat tertulis adalah dua data berbeda. Ambil titik saat berada di lokasi usaha, lalu tetap tulis alamat lengkap dengan patokan yang mudah ditemukan.
    </p>
    <div class="mt-3 flex flex-col gap-2 sm:flex-row">
        <button type="button" x-on:click="useCurrentLocation()" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-500">
            Pakai lokasi usaha saat ini
        </button>
        <button type="button" x-on:click="fillFromMapsText()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Ambil dari link Maps
        </button>
        <button type="button" x-on:click="openSavedPoint()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Cek titik tersimpan
        </button>
        <button type="button" x-on:click="openMaps()" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-50">
            Buka Google Maps
        </button>
    </div>
    <div class="mt-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm leading-6">
        <span class="font-semibold text-gray-950" x-text="savedPointUrl ? 'Titik Maps sudah terbaca' : 'Titik Maps belum terbaca'"></span>
        <span class="text-gray-600" x-show="savedPointUrl"> - cek kembali sebelum menyimpan.</span>
    </div>
    <p x-show="message" x-text="message" class="mt-3 text-sm leading-6 text-gray-600"></p>
</div>
