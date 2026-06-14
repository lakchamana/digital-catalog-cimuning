@props([
    'slug' => 'default',
    'class' => 'h-7 w-7',
])

@php
    $icon = match ($slug) {
        'lihat-semua', 'semua', 'all' => 'grid',
        'kuliner', 'event-catering' => 'utensils',
        'fashion' => 'shirt',
        'jasa' => 'tools',
        'toko-harian' => 'shop',
        'kecantikan' => 'sparkle',
        'digital', 'elektronik' => 'screen',
        'otomotif' => 'car',
        'produk-kreatif' => 'gift',
        'pendidikan' => 'book',
        'kesehatan' => 'health',
        'laundry' => 'laundry',
        'agribisnis' => 'leaf',
        'properti-rumah' => 'home',
        'anak-bayi' => 'baby',
        default => 'tag',
    };
@endphp

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
    @switch($icon)
        @case('grid')
            <path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h3A1.5 1.5 0 0 1 10 5.5v3A1.5 1.5 0 0 1 8.5 10h-3A1.5 1.5 0 0 1 4 8.5v-3Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M14 5.5A1.5 1.5 0 0 1 15.5 4h3A1.5 1.5 0 0 1 20 5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 14 8.5v-3Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M4 15.5A1.5 1.5 0 0 1 5.5 14h3a1.5 1.5 0 0 1 1.5 1.5v3A1.5 1.5 0 0 1 8.5 20h-3A1.5 1.5 0 0 1 4 18.5v-3Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M14 15.5a1.5 1.5 0 0 1 1.5-1.5h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a1.5 1.5 0 0 1-1.5-1.5v-3Z" stroke="currentColor" stroke-width="1.8"/>
            @break

        @case('utensils')
            <path d="M7 4v7m-2-7v7m4-7v7M5 11h4l-.5 9h-3L5 11Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M16 4c2 1.4 3 3.3 3 5.8 0 2.2-.8 4-2.2 4.7V20h-3V4H16Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break

        @case('shirt')
            <path d="m8 5 4 2 4-2 3 3-2 3v8H7v-8L5 8l3-3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M9 5c.6 1.4 1.6 2 3 2s2.4-.6 3-2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('tools')
            <path d="M14 6.5a4 4 0 0 0 4.7 5.2l-6.9 6.9a2.1 2.1 0 0 1-3-3l6.9-6.9A4 4 0 0 0 14 6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="m4.5 5.5 4 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('shop')
            <path d="M5 10h14l-1-5H6l-1 5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M7 10v9h10v-9M10 19v-5h4v5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break

        @case('sparkle')
            <path d="m12 3 1.8 5 5.2 1.9-5.2 1.9L12 17l-1.8-5.2L5 9.9 10.2 8 12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="m18 15 .8 2.2L21 18l-2.2.8L18 21l-.8-2.2L15 18l2.2-.8L18 15Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
            @break

        @case('screen')
            <path d="M5 5h14v10H5V5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 19h6M12 15v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('car')
            <path d="M6 16h12l1-4-2-5H7l-2 5 1 4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M7 16v2m10-2v2M7 12h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('gift')
            <path d="M4 10h16v10H4V10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 10v10M4 14h16M8 10c-1.8 0-3-1.1-3-2.5S6.1 5 7.5 5C9.3 5 12 10 12 10s2.7-5 4.5-5C17.9 5 19 6.1 19 7.5S17.8 10 16 10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            @break

        @case('book')
            <path d="M5 5.5A2.5 2.5 0 0 1 7.5 3H20v16H7.5A2.5 2.5 0 0 0 5 21V5.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M5 17.5A2.5 2.5 0 0 1 7.5 15H20" stroke="currentColor" stroke-width="1.8"/>
            @break

        @case('health')
            <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.7A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M12 9v5m-2.5-2.5h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('laundry')
            <path d="M6 3h12v18H6V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M9 6h.1M12 6h3M12 18a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M9.3 13.5c1.6-1.2 3.1 1.2 5.4 0" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
            @break

        @case('leaf')
            <path d="M20 4c-8.5.2-13 3.9-13 9a5 5 0 0 0 5 5c5.1 0 8.8-4.5 9-13Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M5 20c3.5-5 7-8.1 12-10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @case('home')
            <path d="m4 11 8-7 8 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M6 10v10h12V10M10 20v-5h4v5" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            @break

        @case('baby')
            <path d="M12 20a6 6 0 0 0 6-6v-2a6 6 0 0 0-12 0v2a6 6 0 0 0 6 6Z" stroke="currentColor" stroke-width="1.8"/>
            <path d="M9.5 12h.1m4.8 0h.1M10 16c1.2.8 2.8.8 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            <path d="M8 9c.9-2.2 2.5-3.3 4-3.3S15.1 6.8 16 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            @break

        @default
            <path d="m4 12 8-8h6v6l-8 8-6-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
            <path d="M15.5 7.5h.1" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"/>
    @endswitch
</svg>
