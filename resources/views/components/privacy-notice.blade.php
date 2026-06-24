<div
    x-data="{
        open: false,
        storageKey: 'cimuning_privacy_notice_seen_v1',
        close() {
            this.open = false;

            try {
                window.localStorage.setItem(this.storageKey, '1');
            } catch (error) {
                window.__cimuningPrivacyNoticeSeen = true;
            }
        },
    }"
    x-init="
        let hasSeen = false;

        try {
            hasSeen = window.localStorage.getItem(storageKey) === '1';
        } catch (error) {
            hasSeen = window.__cimuningPrivacyNoticeSeen === true;
        }

        open = ! hasSeen;
    "
    data-privacy-notice="cimuning_privacy_notice_seen_v1"
>
    <section
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed inset-x-0 bottom-0 z-[65] border-t border-cimuning-border bg-white/95 px-4 py-4 shadow-[0_-8px_24px_rgba(16,24,40,0.12)] backdrop-blur"
        aria-label="Pemberitahuan privasi"
    >
        <div class="mx-auto flex max-w-5xl flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <p class="text-sm leading-6 text-cimuning-slate">
                Cimuning Digital Hub memakai data seperlunya untuk pencarian, akun owner, verifikasi UMKM, dan katalog publik. Kami tidak menyimpan tracking klik WhatsApp/Maps atau scan QR.
                <a href="{{ route('privacy') }}" class="font-semibold text-cimuning-red underline">Baca Kebijakan Privasi</a>.
            </p>
            <button
                type="button"
                x-on:click="close()"
                class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white transition hover:bg-cimuning-deep focus:outline-2"
            >
                Mengerti
            </button>
        </div>
    </section>
</div>
