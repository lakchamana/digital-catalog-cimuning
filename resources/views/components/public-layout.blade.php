@props([
    'title' => 'Cimuning Digital Hub',
    'description' => 'Cimuning Digital Hub adalah platform direktori UMKM lokal Cimuning yang mengintegrasikan lokasi Google Maps, katalog produk digital, dan jalur kontak langsung ke pelaku usaha.',
    'canonical' => null,
    'image' => null,
    'type' => 'website',
    'structuredData' => null,
])

@php
    $siteName = config('app.name', 'Cimuning Digital Hub');
    $pageTitle = $title === $siteName ? $siteName : "{$title} - {$siteName}";
    $canonicalUrl = $canonical ?: url()->current();
    $socialImage = $image ?: asset('assets/brand/logo-cimuning.png');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="{{ $description }}">

        <title>{{ $pageTitle }}</title>
        <link rel="canonical" href="{{ $canonicalUrl }}">
        <link rel="icon" type="image/png" href="{{ asset('assets/brand/logo-cimuning.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/brand/logo-cimuning.png') }}">
        <meta property="og:site_name" content="{{ $siteName }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $description }}">
        <meta property="og:type" content="{{ $type }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $socialImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $description }}">
        <meta name="twitter:image" content="{{ $socialImage }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        @if ($structuredData)
            <script type="application/ld+json">
                {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
            </script>
        @endif
    </head>
    <body class="min-h-screen bg-cimuning-white font-sans text-base antialiased">
        <a href="#main-content" class="sr-only z-[90] rounded-button bg-cimuning-red px-5 py-3 text-sm font-semibold text-white focus:not-sr-only focus:fixed focus:left-4 focus:top-4">
            Lewati ke konten utama
        </a>

        <x-navbar />

        <main id="main-content" tabindex="-1">
            {{ $slot }}
        </main>

        <x-footer />
        <x-support-whatsapp />
        @unless (request()->routeIs('privacy'))
            <x-privacy-notice />
        @endunless
        <x-first-visit-onboarding />

        @livewireScripts
    </body>
</html>
