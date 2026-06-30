@php
    $phone = preg_replace('/\D+/', '', (string) config('support.whatsapp'));
    $message = (string) config('support.whatsapp_message');
    $url = 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
    $hasMobileStickyCta = request()->routeIs('umkm.show', 'products.show');
@endphp

<div
    x-data="{
        open: true,
        storageKey: 'cimuning_support_hidden_session_v1',
        close() {
            this.open = false;

            try {
                window.sessionStorage.setItem(this.storageKey, '1');
            } catch (error) {
                window.__cimuningSupportHidden = true;
            }
        },
    }"
    x-init="
        try {
            open = window.sessionStorage.getItem(storageKey) !== '1';
        } catch (error) {
            open = window.__cimuningSupportHidden !== true;
        }
    "
    x-cloak
    x-show="open"
    x-transition.opacity
    data-support-whatsapp
    @class([
        'fixed right-4 z-50 h-14 w-14 md:right-6',
        'bottom-24 lg:bottom-6' => $hasMobileStickyCta,
        'bottom-5 md:bottom-6' => ! $hasMobileStickyCta,
    ])
>
    <a
        href="{{ $url }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Hubungi bantuan Cimuning Digital Hub melalui WhatsApp"
        title="Bantuan WhatsApp"
        class="group relative inline-flex h-14 w-14 items-center justify-center rounded-full bg-cimuning-whatsapp text-white shadow-[0_8px_28px_rgba(16,185,129,0.32)] transition hover:-translate-y-0.5 hover:brightness-95 focus:outline-2 focus:outline-offset-2"
    >
        <svg class="h-7 w-7" viewBox="0 0 32 32" fill="currentColor" aria-hidden="true">
            <path d="M16.04 3.2A12.7 12.7 0 0 0 5.1 22.33L3.3 28.8l6.62-1.74A12.75 12.75 0 1 0 16.04 3.2Zm0 22.95c-2.02 0-4-.55-5.72-1.58l-.41-.25-3.93 1.03 1.05-3.83-.27-.42a10.2 10.2 0 1 1 9.28 5.05Zm5.6-7.64c-.3-.15-1.82-.9-2.1-1-.28-.1-.49-.15-.7.15-.2.3-.79 1-.97 1.2-.18.2-.36.23-.67.08-.3-.15-1.3-.48-2.47-1.53a9.3 9.3 0 0 1-1.71-2.12c-.18-.3-.02-.47.13-.62.14-.14.3-.36.46-.54.15-.18.2-.3.3-.51.1-.2.05-.38-.03-.54-.08-.15-.7-1.67-.95-2.29-.25-.6-.5-.52-.7-.53h-.59c-.2 0-.54.08-.82.38-.28.3-1.08 1.05-1.08 2.57 0 1.51 1.1 2.98 1.26 3.18.15.2 2.17 3.31 5.25 4.64.73.32 1.3.5 1.75.65.73.23 1.4.2 1.93.12.59-.09 1.82-.74 2.08-1.46.25-.72.25-1.34.18-1.46-.08-.13-.28-.2-.59-.36Z"/>
        </svg>

        <span class="pointer-events-none absolute right-full mr-3 hidden whitespace-nowrap rounded-button bg-cimuning-charcoal px-3 py-2 text-xs font-semibold text-white opacity-0 shadow-card transition group-hover:opacity-100 group-focus-visible:opacity-100 md:block">
            Butuh bantuan?
        </span>
    </a>

    <button
        type="button"
        x-on:click="close()"
        class="absolute -right-3 -top-4 z-10 inline-flex h-11 w-11 items-center justify-center rounded-full focus:outline-2 focus:outline-offset-1"
        aria-label="Tutup tombol bantuan WhatsApp"
        title="Tutup bantuan"
    >
        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-cimuning-border bg-white text-lg leading-none text-cimuning-slate shadow-card" aria-hidden="true">&times;</span>
    </button>
</div>
