<div
    x-data="{
        message: '',
        messageType: 'info',
        savedPointUrl: '',
        isLocating: false,
        findInput(name) {
            return document.querySelector(`input[name='data.${name}'], input[name='data[${name}]'], input[id$='${name}']`);
        },
        findAddressInput() {
            return document.querySelector(`textarea[name='data.address'], textarea[name='data[address]'], textarea[id$='address']`);
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
        updateSavedPoint() {
            const latitude = this.findInput('latitude')?.value;
            const longitude = this.findInput('longitude')?.value;

            this.savedPointUrl = latitude && longitude
                ? `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`
                : '';
        },
        setCoordinates(latitude, longitude) {
            this.setInput('latitude', latitude);
            this.setInput('longitude', longitude);

            const url = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;

            this.setInput('maps_link', url);
            this.savedPointUrl = url;
        },
        useCurrentLocation() {
            if (! window.isSecureContext && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
                this.messageType = 'error';
                this.message = 'Lokasi perangkat hanya dapat digunakan melalui koneksi website yang aman.';
                return;
            }

            if (! navigator.geolocation) {
                this.messageType = 'error';
                this.message = 'Perangkat ini tidak menyediakan fitur lokasi. Gunakan Google Maps untuk menentukan titik usaha.';
                return;
            }

            this.isLocating = true;
            this.messageType = 'info';
            this.message = 'Sedang mengambil lokasi perangkat. Pastikan Anda sedang berada di tempat usaha.';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude.toFixed(7);
                    const longitude = position.coords.longitude.toFixed(7);

                    this.setCoordinates(latitude, longitude);
                    this.isLocating = false;
                    this.messageType = position.coords.accuracy > 100 ? 'warning' : 'success';
                    this.message = position.coords.accuracy > 100
                        ? 'Lokasi berhasil diambil, tetapi ketepatannya masih rendah. Buka titik tersimpan dan periksa kembali.'
                        : 'Lokasi berhasil diambil. Buka titik tersimpan untuk memastikan penandanya sudah tepat.';
                },
                (error) => {
                    this.isLocating = false;
                    this.messageType = 'error';
                    this.message = error.code === 1
                        ? 'Izin lokasi belum diberikan. Izinkan akses lokasi di browser atau gunakan Google Maps.'
                        : error.code === 3
                            ? 'Pengambilan lokasi terlalu lama. Coba kembali di tempat dengan sinyal yang lebih baik.'
                            : 'Lokasi perangkat belum ditemukan. Gunakan Google Maps untuk menentukan titik usaha.';
                },
                { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 },
            );
        },
        openSavedPoint() {
            this.updateSavedPoint();

            if (! this.savedPointUrl) {
                this.messageType = 'warning';
                this.message = 'Titik lokasi belum tersedia. Gunakan lokasi perangkat atau tempel link Google Maps terlebih dahulu.';
                return;
            }

            window.open(this.savedPointUrl, '_blank', 'noopener');
        },
        openMaps() {
            const address = this.findAddressInput()?.value?.trim();
            const query = encodeURIComponent(address || 'Cimuning Mustikajaya Bekasi');

            if (! address) {
                this.messageType = 'info';
                this.message = 'Alamat belum diisi. Google Maps akan membuka pencarian wilayah Cimuning.';
            }

            window.open(`https://www.google.com/maps/search/?api=1&query=${query}`, '_blank', 'noopener');
        },
    }"
    x-init="updateSavedPoint()"
    x-on:input.window="updateSavedPoint()"
    class="location-assistant"
    data-location-assistant
>
    <div class="location-assistant-heading">
        <span class="location-assistant-heading-icon" aria-hidden="true">
            <x-filament::icon icon="heroicon-o-map-pin" />
        </span>
        <span>
            <strong>Tentukan titik lokasi usaha</strong>
            <small>Pilih cara yang paling mudah bagi Anda.</small>
        </span>
    </div>

    <div class="location-assistant-actions">
        <button
            type="button"
            class="location-action location-action-primary"
            x-on:click="useCurrentLocation()"
            x-bind:disabled="isLocating"
        >
            <x-filament::icon icon="heroicon-o-cursor-arrow-rays" aria-hidden="true" />
            <span>
                <strong x-text="isLocating ? 'Mengambil lokasi...' : 'Gunakan lokasi perangkat'"></strong>
                <small>Tekan saat berada di tempat usaha</small>
            </span>
        </button>

        <button type="button" class="location-action" x-on:click="openMaps()">
            <x-filament::icon icon="heroicon-o-magnifying-glass" aria-hidden="true" />
            <span>
                <strong>Cari alamat di Google Maps</strong>
                <small>Temukan tempat, lalu salin link lengkap</small>
            </span>
        </button>

        <button type="button" class="location-action" x-on:click="openSavedPoint()">
            <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" aria-hidden="true" />
            <span>
                <strong>Buka titik tersimpan</strong>
                <small>Pastikan penanda berada di tempat yang benar</small>
            </span>
        </button>
    </div>

    <div
        class="location-status"
        x-bind:data-type="message ? messageType : (savedPointUrl ? 'success' : 'neutral')"
        role="status"
        aria-live="polite"
    >
        <x-filament::icon
            x-show="savedPointUrl && ! message"
            icon="heroicon-o-check-circle"
            aria-hidden="true"
        />
        <x-filament::icon
            x-show="! savedPointUrl && ! message"
            icon="heroicon-o-information-circle"
            aria-hidden="true"
        />
        <span x-show="! message" x-text="savedPointUrl ? 'Titik lokasi sudah tersedia. Buka dan periksa sebelum menyimpan.' : 'Titik lokasi belum ditambahkan. Bagian ini boleh dilengkapi setelah alamat tertulis.'"></span>
        <span x-show="message" x-text="message"></span>
    </div>

    <p class="location-address-note">
        <strong>Alamat dan titik Maps berbeda.</strong> Alamat dibaca warga, sedangkan titik Maps membantu membuka arah lokasi.
    </p>

    <style>
        .location-assistant {
            display: grid;
            gap: 1rem;
            border: 1px solid color-mix(in oklab, var(--gray-500) 18%, transparent);
            border-radius: 0.5rem;
            padding: 1rem;
            background: color-mix(in oklab, var(--gray-50) 72%, transparent);
        }

        .location-assistant-heading {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .location-assistant-heading > span:last-child,
        .location-action > span {
            display: grid;
            min-width: 0;
            gap: 0.125rem;
        }

        .location-assistant-heading strong,
        .location-action strong {
            color: var(--gray-950);
            font-size: 0.875rem;
            line-height: 1.3;
        }

        .location-assistant-heading small,
        .location-action small {
            color: var(--gray-500);
            font-size: 0.75rem;
            line-height: 1.35;
        }

        .location-assistant-heading-icon {
            display: inline-flex;
            width: 2.25rem;
            height: 2.25rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            background: color-mix(in oklab, var(--primary-100) 80%, transparent);
            color: var(--primary-700);
        }

        .location-assistant-heading-icon .fi-icon,
        .location-action > .fi-icon,
        .location-status > .fi-icon {
            width: 1.125rem;
            height: 1.125rem;
            flex: 0 0 auto;
        }

        .location-assistant-actions {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 0.5rem;
        }

        .location-action {
            display: grid;
            grid-template-columns: 1.25rem minmax(0, 1fr);
            min-height: 3.5rem;
            align-items: center;
            gap: 0.625rem;
            border: 1px solid color-mix(in oklab, var(--gray-500) 22%, transparent);
            border-radius: 0.5rem;
            padding: 0.625rem 0.75rem;
            text-align: left;
            background: white;
            transition: border-color 150ms ease, background-color 150ms ease;
        }

        .location-action:hover {
            border-color: color-mix(in oklab, var(--gray-500) 42%, transparent);
            background: var(--gray-50);
        }

        .location-action:focus-visible {
            outline: 2px solid var(--primary-600);
            outline-offset: 2px;
        }

        .location-action:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .location-action > .fi-icon {
            color: var(--gray-500);
        }

        .location-action-primary {
            border-color: var(--primary-600);
            background: var(--primary-600);
        }

        .location-action-primary:hover {
            border-color: var(--primary-500);
            background: var(--primary-500);
        }

        .location-action-primary strong,
        .location-action-primary small,
        .location-action-primary > .fi-icon {
            color: white;
        }

        .location-action-primary small {
            opacity: 0.82;
        }

        .location-status {
            display: grid;
            grid-template-columns: 1.125rem minmax(0, 1fr);
            align-items: start;
            gap: 0.5rem;
            border-radius: 0.5rem;
            padding: 0.75rem;
            background: color-mix(in oklab, var(--gray-200) 55%, transparent);
            color: var(--gray-700);
            font-size: 0.8125rem;
            line-height: 1.45;
        }

        .location-status[data-type='success'] {
            background: color-mix(in oklab, var(--success-100) 72%, transparent);
            color: var(--success-700);
        }

        .location-status[data-type='warning'] {
            background: color-mix(in oklab, var(--warning-100) 72%, transparent);
            color: var(--warning-700);
        }

        .location-status[data-type='error'] {
            background: color-mix(in oklab, var(--danger-100) 72%, transparent);
            color: var(--danger-700);
        }

        .location-address-note {
            color: var(--gray-500);
            font-size: 0.75rem;
            line-height: 1.5;
        }

        .location-address-note strong {
            color: var(--gray-700);
        }

        .dark .location-assistant {
            background: color-mix(in oklab, var(--gray-900) 70%, transparent);
        }

        .dark .location-action {
            background: var(--gray-900);
        }

        .dark .location-action:hover {
            background: var(--gray-800);
        }

        .dark .location-action-primary {
            background: var(--primary-600);
        }

        .dark .location-assistant-heading strong,
        .dark .location-action:not(.location-action-primary) strong {
            color: white;
        }

        .dark .location-status,
        .dark .location-address-note strong {
            color: var(--gray-300);
        }

        @media (min-width: 768px) {
            .location-assistant-actions {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }
    </style>
</div>
