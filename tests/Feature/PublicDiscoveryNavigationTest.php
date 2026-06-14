<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicDiscoveryNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_navbar_search_targets_products_index(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('action="'.route('products.index').'"', false)
            ->assertSee('name="search"', false)
            ->assertSee('Cari produk, jasa, atau UMKM Cimuning');
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
}
