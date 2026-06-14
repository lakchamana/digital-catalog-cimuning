<?php

use App\Http\Controllers\LeadRedirectController;
use App\Http\Controllers\QrCodeController;
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
    $featuredProducts = collect();

    if ($hasTables(['categories', 'umkms'])) {
        $categories = Category::query()
            ->where('is_active', true)
            ->withCount(['umkms' => fn ($query) => $query->where('is_active', true)->where('status', 'verified')])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(11)
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

    if ($hasTables(['categories', 'umkms', 'products'])) {
        $featuredProducts = Product::query()
            ->with([
                'category',
                'umkm',
                'images' => fn ($query) => $query->orderBy('sort_order')->orderBy('id'),
            ])
            ->where('is_active', true)
            ->whereHas('umkm', fn ($query) => $query->where('is_active', true)->where('status', 'verified'))
            ->latest()
            ->limit(8)
            ->get();
    }

    return view('home', compact('categories', 'featuredUmkms', 'featuredProducts'));
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

Route::get('/kategori', function () use ($hasTables) {
    if (! $hasTables(['categories', 'umkms'])) {
        return view('categories.index', [
            'categories' => collect(),
        ]);
    }

    $categories = Category::query()
        ->where('is_active', true)
        ->withCount(['umkms' => fn ($query) => $query->where('is_active', true)->where('status', 'verified')])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    return view('categories.index', compact('categories'));
})->name('categories.index');

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

Route::get('/qr/umkm/{umkm:slug}.svg', [QrCodeController::class, 'svg'])
    ->name('qr.umkm.svg');

Route::get('/qr/umkm/{umkm:slug}/open', [QrCodeController::class, 'open'])
    ->name('qr.umkm.open');

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

Route::get('/sitemap.xml', function () use ($hasTables) {
    $urls = collect([
        ['loc' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
        ['loc' => route('products.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => route('umkm.index'), 'priority' => '0.9', 'changefreq' => 'daily'],
        ['loc' => route('categories.index'), 'priority' => '0.8', 'changefreq' => 'weekly'],
        ['loc' => route('umkm.register'), 'priority' => '0.5', 'changefreq' => 'monthly'],
        ['loc' => route('about'), 'priority' => '0.4', 'changefreq' => 'monthly'],
        ['loc' => route('contact'), 'priority' => '0.4', 'changefreq' => 'monthly'],
    ]);

    if ($hasTables(['categories'])) {
        Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->each(fn (Category $category) => $urls->push([
                'loc' => route('categories.show', $category->slug),
                'lastmod' => optional($category->updated_at)->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'weekly',
            ]));
    }

    if ($hasTables(['umkms'])) {
        Umkm::query()
            ->where('is_active', true)
            ->where('status', 'verified')
            ->latest('updated_at')
            ->get()
            ->each(fn (Umkm $umkm) => $urls->push([
                'loc' => route('umkm.show', $umkm->slug),
                'lastmod' => optional($umkm->updated_at)->toAtomString(),
                'priority' => '0.8',
                'changefreq' => 'weekly',
            ]));
    }

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/daftar-umkm', function () {
    return view('umkm.register');
})->name('umkm.register');

Route::get('/leads/{umkm:slug}/{type}', LeadRedirectController::class)
    ->name('leads.redirect');

Route::view('/tentang', 'pages.about')->name('about');

Route::view('/kontak', 'pages.contact')->name('contact');
