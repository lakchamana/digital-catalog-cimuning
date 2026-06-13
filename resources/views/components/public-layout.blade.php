@props(['title' => 'Cimuning UMKM Online Directory'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Direktori online UMKM Cimuning, Kota Bekasi untuk mencari produk, jasa, dan kontak usaha lokal.">

        <title>{{ $title }} - {{ config('app.name', 'Cimuning UMKM') }}</title>

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
