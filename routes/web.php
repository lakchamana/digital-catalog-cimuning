<?php

use App\Models\Category;
use App\Models\Product;
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
    $search = request('search');
    $products = collect();

    if ($hasTables(['products', 'umkms', 'categories'])) {
        $products = Product::query()
            ->with(['umkm', 'category'])
            ->where('is_active', true)
            ->whereHas('umkm', fn ($query) => $query->where('is_active', true)->where('status', 'verified'))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('umkm', fn ($umkmQuery) => $umkmQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('category', fn ($categoryQuery) => $categoryQuery->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->get();
    }

    return view('products.index', compact('products', 'search'));
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
        ->with(['category', 'products' => fn ($query) => $query->where('is_active', true)->latest(), 'contacts', 'socialLinks'])
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
    'heading' => 'Tentang Cimuning UMKM Online Directory',
    'description' => 'Platform ini membantu warga menemukan UMKM lokal dan menghubungi pelaku usaha secara langsung.',
])->name('about');

Route::view('/kontak', 'pages.placeholder', [
    'title' => 'Kontak',
    'heading' => 'Kontak Pengelola',
    'description' => 'Informasi kontak pengelola akan ditambahkan setelah struktur admin dan data platform disiapkan.',
])->name('contact');
