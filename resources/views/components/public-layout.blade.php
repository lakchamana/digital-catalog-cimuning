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
        <x-navbar />

        <main>
            {{ $slot }}
        </main>

        <x-footer />

        @livewireScripts
    </body>
</html>
