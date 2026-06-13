<?php

use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

$hasTables = static function (array $tables): bool {
    try {
        foreach ($tables as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    } catch (Throwable) {
        return false;
    }
};

Route::get('/', function () use ($hasTables) {
    $categories = collect();
    $featuredUmkms = collect();

    if ($hasTables(['categories', 'umkms'])) {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['umkms' => fn ($query) => $query->where('is_active', true)->where('status', 'verified')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $featuredUmkms = Umkm::query()
            ->with('category')
            ->where('is_active', true)
            ->where('status', 'verified')
            ->where('is_featured', true)
            ->latest()
            ->limit(3)
            ->get();
    }

    return view('home', compact('categories', 'featuredUmkms'));
})->name('home');

Route::get('/umkm', function () use ($hasTables) {
    abort_unless($hasTables(['categories', 'umkms', 'products']), 503);

    return view('umkm.index', [
        'category' => request('category'),
    ]);
})->name('umkm.index');

Route::get('/produk', function () use ($hasTables) {
    abort_unless($hasTables(['products', 'umkms', 'categories']), 503);

    return view('products.index');
})->name('products.index');

Route::get('/kategori/{slug}', function (string $slug) use ($hasTables) {
    if (! $hasTables(['categories', 'umkms'])) {
        abort(404);
    }

    $category = Category::query()->where('slug', $slug)->where('is_active', true)->firstOrFail();

    return view('umkm.index', [
        'category' => $category->slug,
        'pageTitle' => "Kategori {$category->name}",
    ]);
})->name('categories.show');

Route::get('/umkm/{slug}', function (string $slug) use ($hasTables) {
    if (! $hasTables(['umkms', 'products', 'categories'])) {
        abort(404);
    }

    $umkm = Umkm::query()
        ->with([
            'category',
            'products' => fn ($query) => $query
                ->with(['category', 'images' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
                ->where('is_active', true)
                ->latest(),
            'contacts',
            'socialLinks',
        ])
        ->where('is_active', true)
        ->where('status', 'verified')
        ->where('slug', $slug)
        ->firstOrFail();

    return view('umkm.show', compact('umkm'));
})->name('umkm.show');

Route::view('/daftar-umkm', 'pages.placeholder', [
    'title' => 'Daftarkan UMKM',
    'heading' => 'Daftarkan UMKM Anda',
    'description' => 'Form pendaftaran UMKM akan dibuat dengan validasi, upload foto, dan alur verifikasi pada fase dashboard.',
])->name('umkm.register');

Route::view('/tentang', 'pages.placeholder', [
    'title' => 'Tentang',
    'heading' => 'Tentang Cimuning Digital Hub',
    'description' => 'Platform ini membantu warga menemukan UMKM lokal Cimuning, melihat katalog produk digital, membuka lokasi Google Maps, dan menghubungi pelaku usaha secara langsung.',
])->name('about');

Route::view('/kontak', 'pages.placeholder', [
    'title' => 'Kontak',
    'heading' => 'Kontak Pengelola',
    'description' => 'Informasi kontak pengelola akan ditambahkan setelah struktur admin dan data platform disiapkan.',
])->name('contact');
