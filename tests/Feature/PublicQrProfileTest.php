<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicQrProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_umkm_qr_svg_points_directly_to_public_profile(): void
    {
        $umkm = $this->umkm();

        $this->get(route('qr.umkm.svg', $umkm->slug))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('<svg', false)
            ->assertSee(route('umkm.show', $umkm->slug), false);
    }

    public function test_pending_or_inactive_umkm_cannot_render_qr(): void
    {
        $pending = $this->umkm([
            'slug' => 'qr-pending',
            'status' => 'pending',
            'is_active' => false,
        ]);

        $this->get(route('qr.umkm.svg', $pending->slug))->assertNotFound();
    }

    public function test_umkm_detail_renders_qr_share_card(): void
    {
        $umkm = $this->umkm();

        $this->get(route('umkm.show', $umkm->slug))
            ->assertOk()
            ->assertSee('Bagikan Profil UMKM')
            ->assertSee(route('qr.umkm.svg', $umkm->slug), false)
            ->assertSee(route('umkm.show', $umkm->slug), false)
            ->assertSee('Download QR');
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
        $slug = $attributes['slug'] ?? uniqid('qr-umkm-', false);

        return Umkm::query()->create(array_merge([
            'category_id' => $attributes['category_id'] ?? $this->category()->id,
            'name' => str($slug)->replace('-', ' ')->title()->toString(),
            'slug' => $slug,
            'status' => 'verified',
            'is_active' => true,
            'whatsapp' => '081234567890',
            'address' => 'Jl. Cimuning QR',
        ], $attributes));
    }
}
