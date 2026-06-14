@props(['title' => 'Cimuning Digital Hub'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Cimuning Digital Hub adalah platform direktori UMKM lokal Cimuning yang mengintegrasikan lokasi Google Maps, katalog produk digital, dan jalur kontak langsung ke pelaku usaha.">

        <title>{{ $title }} - {{ config('app.name', 'Cimuning Digital Hub') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/brand/logo-cimuning.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/brand/logo-cimuning.png') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
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
        <x-first-visit-onboarding />

        @livewireScripts
    </body>
</html>
