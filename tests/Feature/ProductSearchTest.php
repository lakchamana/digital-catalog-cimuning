<?php

namespace Tests\Feature;

use App\Livewire\Public\ProductSearch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_search_matches_product_and_umkm_category(): void
    {
        $this->seedProducts();

        Livewire::test(ProductSearch::class)
            ->set('search', 'Jasa')
            ->call('submitSearch')
            ->assertSet('search', 'Jasa')
            ->assertSee('Hasil untuk')
            ->assertSee('Kata kunci')
            ->assertSee('Paket Jahit')
            ->assertDontSee('Nasi Ayam');
    }

    public function test_product_search_ui_has_clear_primary_search_action(): void
    {
        $this->seedProducts();

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('Cari produk atau jasa')
            ->assertSee('Contoh: nasi box, laundry, servis motor...')
            ->assertSee('wire:submit.prevent="submitSearch"', false)
            ->assertSee('Cari')
            ->assertSee('Saring hasil')
            ->assertSee('Lihat hasil')
            ->assertDontSee('>Reset</button>', false)
            ->assertDontSee('Terapkan Filter');
    }

    public function test_category_filter_matches_product_category_or_umkm_category_fallback(): void
    {
        $this->seedProducts();

        Livewire::test(ProductSearch::class)
            ->set('category', 'kuliner')
            ->assertSee('Nasi Ayam')
            ->assertDontSee('Paket Jahit');

        Livewire::test(ProductSearch::class)
            ->set('category', 'jasa')
            ->assertSee('Paket Jahit')
            ->assertDontSee('Nasi Ayam');
    }

    public function test_umkm_and_price_filters_only_show_matching_public_products(): void
    {
        $this->seedProducts();

        Livewire::test(ProductSearch::class)
            ->set('umkm', 'dapur-filter')
            ->assertSee('Nasi Ayam')
            ->assertDontSee('Paket Jahit');

        Livewire::test(ProductSearch::class)
            ->set('price', 'priced')
            ->assertSee('Nasi Ayam')
            ->assertDontSee('Paket Jahit')
            ->assertDontSee('Konsultasi Gratis');

        Livewire::test(ProductSearch::class)
            ->set('price', 'contact')
            ->assertSee('Paket Jahit')
            ->assertSee('Konsultasi Gratis')
            ->assertDontSee('Nasi Ayam');
    }

    public function test_invalid_filter_state_falls_back_to_safe_defaults(): void
    {
        $this->seedProducts();

        $this->get(route('products.index', [
            'category' => 'kategori-tidak-ada',
            'umkm' => 'umkm-tidak-ada',
            'price' => 'aneh',
            'sort' => 'aneh',
            'perPage' => '999',
        ]))
            ->assertOk()
            ->assertSee('Nasi Ayam')
            ->assertSee('Paket Jahit');

        Livewire::test(ProductSearch::class)
            ->set('category', 'kategori-tidak-ada')
            ->set('umkm', 'umkm-tidak-ada')
            ->set('price', 'aneh')
            ->set('sort', 'aneh')
            ->set('perPage', '999')
            ->assertSet('category', '')
            ->assertSet('umkm', '')
            ->assertSet('price', 'all')
            ->assertSet('sort', 'latest')
            ->assertSet('perPage', 9)
            ->assertSee('Nasi Ayam')
            ->assertSee('Paket Jahit');
    }

    public function test_reset_filters_restores_default_state(): void
    {
        $this->seedProducts();

        Livewire::test(ProductSearch::class)
            ->set('search', 'Nasi')
            ->set('category', 'kuliner')
            ->set('price', 'priced')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('category', '')
            ->assertSet('price', 'all')
            ->assertSet('sort', 'latest')
            ->assertSet('perPage', 9);
    }

    public function test_filter_chips_can_clear_individual_filters(): void
    {
        $this->seedProducts();

        Livewire::test(ProductSearch::class)
            ->set('search', 'Nasi')
            ->set('category', 'kuliner')
            ->set('price', 'priced')
            ->assertSee('Kata kunci')
            ->assertSee('Kategori')
            ->assertSee('Harga')
            ->call('clearFilter', 'search')
            ->assertSet('search', '')
            ->assertSet('category', 'kuliner')
            ->assertSet('price', 'priced')
            ->assertSee('Produk kategori Kuliner')
            ->call('clearFilter', 'category')
            ->assertSet('category', '')
            ->assertSet('price', 'priced');
    }

    private function seedProducts(): void
    {
        $kuliner = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $jasa = Category::query()->create([
            'name' => 'Jasa',
            'slug' => 'jasa',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $dapur = Umkm::query()->create([
            'category_id' => $kuliner->id,
            'name' => 'Dapur Filter',
            'slug' => 'dapur-filter',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $jahit = Umkm::query()->create([
            'category_id' => $jasa->id,
            'name' => 'Jahit Filter',
            'slug' => 'jahit-filter',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $pending = Umkm::query()->create([
            'category_id' => $kuliner->id,
            'name' => 'Pending Filter',
            'slug' => 'pending-filter',
            'status' => 'pending',
            'is_active' => false,
        ]);

        Product::query()->create([
            'umkm_id' => $dapur->id,
            'category_id' => $kuliner->id,
            'name' => 'Nasi Ayam',
            'slug' => 'nasi-ayam-filter',
            'description' => 'Nasi ayam rumahan.',
            'price' => 25000,
            'is_active' => true,
        ]);

        Product::query()->create([
            'umkm_id' => $jahit->id,
            'category_id' => null,
            'name' => 'Paket Jahit',
            'slug' => 'paket-jahit-filter',
            'description' => 'Layanan jahit pakaian.',
            'price' => null,
            'is_active' => true,
        ]);

        Product::query()->create([
            'umkm_id' => $jahit->id,
            'category_id' => null,
            'name' => 'Konsultasi Gratis',
            'slug' => 'konsultasi-gratis-filter',
            'description' => 'Konsultasi ukuran.',
            'price' => 0,
            'is_active' => true,
        ]);

        Product::query()->create([
            'umkm_id' => $pending->id,
            'category_id' => $kuliner->id,
            'name' => 'Produk Pending Filter',
            'slug' => 'produk-pending-filter',
            'price' => 10000,
            'is_active' => true,
        ]);
    }
}
