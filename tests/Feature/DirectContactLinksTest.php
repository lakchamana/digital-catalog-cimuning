<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DirectContactLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_lead_storage_and_redirect_route_are_removed(): void
    {
        $this->assertFalse(Schema::hasTable('lead_events'));
        $this->assertFalse(Route::has('leads.redirect'));
        $this->assertFalse(Route::has('qr.umkm.open'));
    }

    public function test_umkm_detail_links_directly_to_whatsapp_and_maps(): void
    {
        $umkm = $this->umkm();

        $this->get(route('umkm.show', $umkm->slug))
            ->assertOk()
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee('https://www.google.com/maps/search/?api=1&amp;query=', false)
            ->assertSee('Alamat tertulis')
            ->assertSee('Titik Google Maps')
            ->assertDontSee('/leads/', false);
    }

    public function test_product_card_links_directly_to_whatsapp_with_product_message(): void
    {
        $umkm = $this->umkm();
        $product = Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $umkm->category_id,
            'name' => 'Nasi Kuning Langsung',
            'slug' => 'nasi-kuning-langsung',
            'is_active' => true,
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee(urlencode("Halo, saya ingin bertanya tentang {$product->name}."), false)
            ->assertDontSee('/leads/', false);
    }

    private function umkm(): Umkm
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner-kontak-langsung',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        return Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Kontak Langsung',
            'slug' => 'dapur-kontak-langsung',
            'status' => 'verified',
            'is_active' => true,
            'whatsapp' => '081234567890',
            'address' => 'Jl. Cimuning Raya',
            'latitude' => -6.3123456,
            'longitude' => 107.0123456,
        ]);
    }
}
