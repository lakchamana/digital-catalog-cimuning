<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_first_visit_onboarding_markup(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('data-onboarding="interactive-walkthrough"', false)
            ->assertSee('cimuning_walkthrough_seen_v1')
            ->assertSee('Cari Produk/Jasa')
            ->assertSee('Buka Kategori')
            ->assertSee('Daftarkan UMKM')
            ->assertSee('Tidak ada cart, checkout, payment, atau ongkir internal.');
    }

    public function test_products_and_umkm_registration_pages_render_onboarding_markup(): void
    {
        $this->seedDirectoryTables();

        $this->get('/produk')
            ->assertOk()
            ->assertSee('data-onboarding="interactive-walkthrough"', false)
            ->assertSee('Cari Produk/Jasa');

        $this->get('/daftar-umkm')
            ->assertOk()
            ->assertSee('data-onboarding="interactive-walkthrough"', false)
            ->assertSee('Daftarkan UMKM');
    }

    private function seedDirectoryTables(): void
    {
        $category = $this->category();

        Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Produk',
            'slug' => 'dapur-produk',
            'status' => 'verified',
            'is_active' => true,
        ]);
    }

    private function category(): Category
    {
        return Category::query()->firstOrCreate(
            ['slug' => 'kuliner'],
            [
                'name' => 'Kuliner',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );
    }
}
