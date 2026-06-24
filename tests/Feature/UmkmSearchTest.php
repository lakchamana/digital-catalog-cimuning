<?php

namespace Tests\Feature;

use App\Livewire\Public\UmkmSearch;
use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UmkmSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_umkm_page_uses_navbar_search_and_live_filters(): void
    {
        $this->seedUmkms();

        $this->get(route('umkm.index'))
            ->assertOk()
            ->assertDontSee('id="umkm-search"', false)
            ->assertDontSee('Contoh: warung nasi, laundry, RW 07...')
            ->assertDontSee('wire:submit.prevent="submitSearch"', false)
            ->assertSee('Gunakan search utama di navbar untuk mengganti kata kunci.')
            ->assertSee('Saring UMKM')
            ->assertSee('Lihat hasil')
            ->assertSee('method="GET"', false)
            ->assertSee('onchange="window.CimuningFilters.submit(this)"', false)
            ->assertSee('name="category"', false)
            ->assertSee('name="services[]"', false)
            ->assertDontSee('wire:model.live="category"', false)
            ->assertDontSee('>Reset</button>', false)
            ->assertDontSee('Terapkan Filter');
    }

    public function test_keyword_search_from_url_uses_contextual_heading(): void
    {
        $this->seedUmkms();

        Livewire::withQueryParams(['search' => 'Dapur'])
            ->test(UmkmSearch::class)
            ->assertSet('search', 'Dapur')
            ->assertSee('Hasil untuk')
            ->assertSee('Kata kunci')
            ->assertSee('Dapur Nasi Cimuning')
            ->assertDontSee('Laundry Bersih Cimuning');
    }

    public function test_category_rw_and_service_filters_show_matching_public_umkms(): void
    {
        $this->seedUmkms();

        Livewire::test(UmkmSearch::class)
            ->set('category', 'kuliner')
            ->assertSee('UMKM kategori Kuliner')
            ->assertSee('Dapur Nasi Cimuning')
            ->assertDontSee('Laundry Bersih Cimuning');

        Livewire::test(UmkmSearch::class)
            ->set('rw', 'RW 07')
            ->assertSee('UMKM di RW 07')
            ->assertSee('Dapur Nasi Cimuning')
            ->assertDontSee('Laundry Bersih Cimuning');

        Livewire::test(UmkmSearch::class)
            ->set('services', ['delivery'])
            ->assertSee('UMKM sesuai layanan')
            ->assertSee('Dapur Nasi Cimuning')
            ->assertDontSee('Laundry Bersih Cimuning');
    }

    public function test_invalid_filter_state_falls_back_to_safe_defaults(): void
    {
        $this->seedUmkms();

        $this->get(route('umkm.index', [
            'category' => 'kategori-tidak-ada',
            'rw' => 'RW 99',
            'services' => ['aneh'],
            'sort' => 'aneh',
            'perPage' => '999',
        ]))
            ->assertOk()
            ->assertSee('Dapur Nasi Cimuning')
            ->assertSee('Laundry Bersih Cimuning');

        Livewire::test(UmkmSearch::class)
            ->set('category', 'kategori-tidak-ada')
            ->set('rw', 'RW 99')
            ->set('services', ['delivery', 'aneh'])
            ->set('sort', 'aneh')
            ->set('perPage', '999')
            ->assertSet('category', '')
            ->assertSet('rw', '')
            ->assertSet('services', ['delivery'])
            ->assertSet('sort', 'latest')
            ->assertSet('perPage', 9)
            ->assertSee('Dapur Nasi Cimuning')
            ->assertDontSee('Laundry Bersih Cimuning');
    }

    public function test_filter_chips_can_clear_individual_filters(): void
    {
        $this->seedUmkms();

        Livewire::withQueryParams(['search' => 'Cimuning'])
            ->test(UmkmSearch::class)
            ->set('category', 'kuliner')
            ->set('rw', 'RW 07')
            ->set('services', ['delivery', 'cod'])
            ->assertSee('Kata kunci')
            ->assertSee('Kategori')
            ->assertSee('RW')
            ->assertSee('Layanan')
            ->call('clearFilter', 'search')
            ->assertSet('search', '')
            ->assertSet('category', 'kuliner')
            ->assertSet('rw', 'RW 07')
            ->call('clearFilter', 'service:delivery')
            ->assertSet('services', ['cod'])
            ->call('clearFilter', 'category')
            ->assertSet('category', '')
            ->assertSet('rw', 'RW 07');
    }

    public function test_reset_filters_restores_default_state_and_keeps_public_visibility(): void
    {
        $this->seedUmkms();

        Livewire::test(UmkmSearch::class)
            ->set('search', 'Dapur')
            ->set('category', 'kuliner')
            ->set('rw', 'RW 07')
            ->set('services', ['delivery'])
            ->set('sort', 'az')
            ->set('perPage', 18)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('category', '')
            ->assertSet('rw', '')
            ->assertSet('services', [])
            ->assertSet('verified', true)
            ->assertSet('sort', 'latest')
            ->assertSet('perPage', 9)
            ->assertDontSee('UMKM Pending Cimuning');
    }

    public function test_removed_popular_sort_falls_back_to_latest(): void
    {
        $this->seedUmkms();

        Livewire::test(UmkmSearch::class)
            ->set('sort', 'popular')
            ->assertSet('sort', 'latest')
            ->assertDontSee('Populer');
    }

    private function seedUmkms(): void
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

        Umkm::query()->create([
            'category_id' => $kuliner->id,
            'name' => 'Dapur Nasi Cimuning',
            'slug' => 'dapur-nasi-cimuning',
            'description' => 'Nasi rumahan dan lauk harian.',
            'rw' => 'RW 07',
            'status' => 'verified',
            'is_active' => true,
            'service_delivery' => true,
            'service_cod' => true,
        ]);

        Umkm::query()->create([
            'category_id' => $jasa->id,
            'name' => 'Laundry Bersih Cimuning',
            'slug' => 'laundry-bersih-cimuning',
            'description' => 'Laundry kiloan warga Cimuning.',
            'rw' => 'RW 08',
            'status' => 'verified',
            'is_active' => true,
            'service_delivery' => false,
            'service_cod' => false,
        ]);

        Umkm::query()->create([
            'category_id' => $kuliner->id,
            'name' => 'UMKM Pending Cimuning',
            'slug' => 'umkm-pending-cimuning',
            'description' => 'Belum tampil publik.',
            'rw' => 'RW 07',
            'status' => 'pending',
            'is_active' => false,
        ]);
    }
}
