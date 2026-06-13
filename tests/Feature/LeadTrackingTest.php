<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LeadEvent;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_whatsapp_click_for_verified_umkm_creates_lead_event_and_redirects(): void
    {
        $umkm = $this->umkm([
            'whatsapp' => '081234567890',
        ]);

        $response = $this->get(route('leads.redirect', [
            'umkm' => $umkm->slug,
            'type' => 'whatsapp',
            'source' => 'detail',
        ]));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://wa.me/6281234567890', $response->headers->get('Location'));
        $this->assertDatabaseHas('lead_events', [
            'umkm_id' => $umkm->id,
            'type' => 'whatsapp',
            'source' => 'detail',
        ]);
    }

    public function test_maps_click_for_verified_umkm_creates_lead_event_and_redirects(): void
    {
        $umkm = $this->umkm([
            'latitude' => -6.3211111,
            'longitude' => 107.0122222,
        ]);

        $response = $this->get(route('leads.redirect', [
            'umkm' => $umkm->slug,
            'type' => 'maps',
            'source' => 'maps_section',
        ]));

        $response->assertRedirect();
        $this->assertStringStartsWith('https://www.google.com/maps/search/', $response->headers->get('Location'));
        $this->assertDatabaseHas('lead_events', [
            'umkm_id' => $umkm->id,
            'type' => 'maps',
            'source' => 'maps_section',
        ]);
    }

    public function test_product_whatsapp_click_stores_product_relation(): void
    {
        $category = $this->category();
        $umkm = $this->umkm([
            'category_id' => $category->id,
            'whatsapp' => '081234567890',
        ]);
        $product = Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Kuning',
            'slug' => 'nasi-kuning',
            'is_active' => true,
        ]);

        $this->get(route('leads.redirect', [
            'umkm' => $umkm->slug,
            'type' => 'whatsapp',
            'product' => $product->id,
            'source' => 'product_card',
        ]))->assertRedirect();

        $this->assertDatabaseHas('lead_events', [
            'umkm_id' => $umkm->id,
            'product_id' => $product->id,
            'type' => 'whatsapp',
            'source' => 'product_card',
        ]);
    }

    public function test_inactive_or_unverified_umkm_cannot_create_lead_event(): void
    {
        $pending = $this->umkm([
            'slug' => 'dapur-pending',
            'status' => 'pending',
            'is_active' => false,
            'whatsapp' => '081234567890',
        ]);

        $this->get(route('leads.redirect', [
            'umkm' => $pending->slug,
            'type' => 'whatsapp',
        ]))->assertNotFound();

        $this->assertDatabaseCount('lead_events', 0);
    }

    public function test_missing_whatsapp_or_maps_target_returns_not_found(): void
    {
        $withoutWhatsapp = $this->umkm(['slug' => 'tanpa-whatsapp', 'whatsapp' => null]);
        $withoutMaps = $this->umkm(['slug' => 'tanpa-maps', 'address' => null]);

        $this->get(route('leads.redirect', [
            'umkm' => $withoutWhatsapp->slug,
            'type' => 'whatsapp',
        ]))->assertNotFound();

        $this->get(route('leads.redirect', [
            'umkm' => $withoutMaps->slug,
            'type' => 'maps',
        ]))->assertNotFound();

        $this->assertDatabaseCount('lead_events', 0);
    }

    public function test_owner_lead_event_scope_only_returns_owned_umkm_events(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Satu',
            'email' => 'owner-satu@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);
        $otherOwner = User::query()->create([
            'name' => 'Owner Dua',
            'email' => 'owner-dua@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $ownedUmkm = $this->umkm(['slug' => 'owned-umkm', 'user_id' => $owner->id]);
        $otherUmkm = $this->umkm(['slug' => 'other-umkm', 'user_id' => $otherOwner->id]);

        $ownedLead = LeadEvent::query()->create([
            'umkm_id' => $ownedUmkm->id,
            'type' => 'whatsapp',
            'source' => 'detail',
            'target_url' => 'https://wa.me/6281234567890',
        ]);
        LeadEvent::query()->create([
            'umkm_id' => $otherUmkm->id,
            'type' => 'maps',
            'source' => 'detail',
            'target_url' => 'https://www.google.com/maps/search/?api=1&query=Cimuning',
        ]);

        $this->assertSame(
            [$ownedLead->id],
            LeadEvent::query()->visibleTo($owner)->pluck('id')->all(),
        );
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

    private function umkm(array $attributes = []): Umkm
    {
        $slug = $attributes['slug'] ?? uniqid('dapur-uji-', false);

        return Umkm::query()->create(array_merge([
            'category_id' => $attributes['category_id'] ?? $this->category()->id,
            'name' => str($slug)->replace('-', ' ')->title()->toString(),
            'slug' => $slug,
            'status' => 'verified',
            'is_active' => true,
            'whatsapp' => '081234567890',
            'address' => 'Jl. Cimuning Raya',
        ], $attributes));
    }
}
