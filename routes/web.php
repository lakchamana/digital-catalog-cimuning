<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::view('/umkm', 'pages.placeholder', [
    'title' => 'Direktori UMKM',
    'heading' => 'Direktori UMKM Cimuning',
    'description' => 'Halaman listing UMKM dengan search, filter, dan pagination Livewire akan dibangun pada tahap berikutnya.',
])->name('umkm.index');

Route::view('/produk', 'pages.placeholder', [
    'title' => 'Produk dan Jasa',
    'heading' => 'Produk dan Jasa UMKM',
    'description' => 'Halaman katalog produk dan jasa akan tersambung dengan data UMKM setelah model dan migration dibuat.',
])->name('products.index');

Route::view('/daftar-umkm', 'pages.placeholder', [
    'title' => 'Daftarkan UMKM',
    'heading' => 'Daftarkan UMKM Anda',
    'description' => 'Form pendaftaran UMKM akan dibuat dengan validasi, upload foto, dan alur verifikasi pada fase dashboard.',
])->name('umkm.register');

Route::view('/tentang', 'pages.placeholder', [
    'title' => 'Tentang',
    'heading' => 'Tentang Cimuning UMKM Online Directory',
    'description' => 'Platform ini membantu warga menemukan UMKM lokal dan menghubungi pelaku usaha secara langsung.',
])->name('about');

Route::view('/kontak', 'pages.placeholder', [
    'title' => 'Kontak',
    'heading' => 'Kontak Pengelola',
    'description' => 'Informasi kontak pengelola akan ditambahkan setelah struktur admin dan data platform disiapkan.',
])->name('contact');
