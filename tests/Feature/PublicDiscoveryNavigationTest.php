<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDiscoveryNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_search_targets_products_index(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="#main-content"', false)
            ->assertSee('<main id="main-content"', false)
            ->assertSee('Lewati ke konten utama')
            ->assertSee('action="'.route('products.index').'"', false)
            ->assertSee('name="search"', false)
            ->assertSee('Cari produk, jasa, atau UMKM Cimuning')
            ->assertSee('aria-current="page"', false)
            ->assertSee('aria-controls="mobile-navigation-drawer"', false)
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false);
    }

    public function test_mobile_navigation_drawer_is_outside_the_sticky_header(): void
    {
        $html = $this->get('/')->assertOk()->getContent();
        $document = new \DOMDocument();

        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($document);

        $this->assertCount(1, $xpath->query('//*[@id="mobile-navigation-drawer"]'));
        $this->assertCount(0, $xpath->query('//*[@id="mobile-navigation-drawer"]/ancestor::header'));
        $this->assertCount(1, $xpath->query('//*[@data-mobile-navigation-backdrop]'));
    }

    public function test_homepage_renders_carousel_and_category_shortcuts(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-carousel="home-jumbotron"', false)
            ->assertSee('data-carousel-track', false)
            ->assertSee('data-carousel-control="prev"', false)
            ->assertSee('data-carousel-control="next"', false)
            ->assertSee('data-carousel-dot', false)
            ->assertSee('IntersectionObserver')
            ->assertDontSee('scrollIntoView')
            ->assertSee('Produk lokal Cimuning')
            ->assertSee('Lihat Semua')
            ->assertSee(route('categories.index'), false)
            ->assertSee('Pendidikan');
    }

    public function test_category_index_only_shows_active_categories(): void
    {
        Category::query()->create([
            'name' => 'Pendidikan',
            'slug' => 'pendidikan',
            'description' => 'Kursus dan layanan edukasi.',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        Category::query()->create([
            'name' => 'Kategori Rahasia',
            'slug' => 'kategori-rahasia',
            'description' => 'Tidak tampil publik.',
            'is_active' => false,
            'sort_order' => 2,
        ]);

        $this->get(route('categories.index'))
            ->assertOk()
            ->assertSee('Jelajahi semua kategori Cimuning')
            ->assertSee('Pendidikan')
            ->assertSee('Kursus dan layanan edukasi.')
            ->assertDontSee('Kategori Rahasia');
    }

    public function test_livewire_listing_pages_render_accessible_filter_and_result_regions(): void
    {
        $this->seedDirectoryContent();

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('aria-controls="product-filter-drawer"', false)
            ->assertSee('id="product-filter-drawer"', false)
            ->assertSee('aria-labelledby="product-filter-title"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('role="status"', false)
            ->assertSee('product-category-filter-desktop', false)
            ->assertSee('product-category-filter-mobile', false);

        $this->get(route('umkm.index'))
            ->assertOk()
            ->assertSee('aria-controls="umkm-filter-drawer"', false)
            ->assertSee('id="umkm-filter-drawer"', false)
            ->assertSee('aria-labelledby="umkm-filter-title"', false)
            ->assertSee('aria-live="polite"', false)
            ->assertSee('role="status"', false)
            ->assertSee('category-filter-desktop', false)
            ->assertSee('category-filter-mobile', false);
    }

    public function test_umkm_listing_opens_directly_on_search_without_a_visual_hero(): void
    {
        $this->seedDirectoryContent();

        $this->get(route('umkm.index'))
            ->assertOk()
            ->assertSee('<h1 class="sr-only">Direktori UMKM</h1>', false)
            ->assertSee('Cari UMKM')
            ->assertDontSee('Temukan UMKM Cimuning')
            ->assertDontSee('Cari berdasarkan nama usaha, produk, jasa, kategori, deskripsi, atau lokasi RW.');

        $this->get(route('categories.show', 'kuliner'))
            ->assertOk()
            ->assertSee('<h1 class="sr-only">Kategori Kuliner</h1>', false)
            ->assertDontSee('Temukan UMKM Cimuning');
    }

    public function test_public_routes_render_after_accessibility_polish(): void
    {
        $umkm = $this->seedDirectoryContent();

        foreach ([
            route('home'),
            route('products.index'),
            route('umkm.index'),
            route('categories.index'),
            route('categories.show', 'kuliner'),
            route('umkm.show', $umkm->slug),
            route('umkm.register'),
            route('about'),
            route('contact'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    private function seedDirectoryContent(): Umkm
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'kuliner'],
            [
                'name' => 'Kuliner',
                'description' => 'Makanan dan minuman lokal.',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $umkm = Umkm::query()->firstOrCreate(
            ['slug' => 'dapur-aksesibel'],
            [
                'category_id' => $category->id,
                'name' => 'Dapur Aksesibel',
                'description' => 'Makanan rumahan untuk warga Cimuning.',
                'status' => 'verified',
                'is_active' => true,
                'whatsapp' => '081234567890',
                'address' => 'Cimuning, Kota Bekasi',
            ],
        );

        Product::query()->firstOrCreate(
            ['slug' => 'nasi-aksesibel'],
            [
                'umkm_id' => $umkm->id,
                'category_id' => $category->id,
                'name' => 'Nasi Aksesibel',
                'description' => 'Nasi box rumahan.',
                'price' => 25000,
                'is_active' => true,
            ],
        );

        return $umkm;
    }
}
