<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LeadEvent;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicQrProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_umkm_qr_svg_contains_open_tracking_url(): void
    {
        $umkm = $this->umkm();

        $this->get(route('qr.umkm.svg', $umkm->slug))
            ->assertOk()
            ->assertHeader('Content-Type', 'image/svg+xml')
            ->assertSee('<svg', false)
            ->assertSee(route('qr.umkm.open', $umkm->slug), false);
    }

    public function test_qr_open_records_scan_and_redirects_to_public_profile(): void
    {
        $umkm = $this->umkm();

        $this->get(route('qr.umkm.open', $umkm->slug))
            ->assertRedirect(route('umkm.show', $umkm->slug));

        $this->assertDatabaseHas('lead_events', [
            'umkm_id' => $umkm->id,
            'type' => 'qr_scan',
            'source' => 'qr_profile',
            'target_url' => route('umkm.show', $umkm->slug),
        ]);
    }

    public function test_pending_or_inactive_umkm_cannot_render_or_track_qr(): void
    {
        $pending = $this->umkm([
            'slug' => 'qr-pending',
            'status' => 'pending',
            'is_active' => false,
        ]);

        $this->get(route('qr.umkm.svg', $pending->slug))->assertNotFound();
        $this->get(route('qr.umkm.open', $pending->slug))->assertNotFound();

        $this->assertDatabaseCount('lead_events', 0);
    }

    public function test_umkm_detail_renders_qr_share_card(): void
    {
        $umkm = $this->umkm();

        $this->get(route('umkm.show', $umkm->slug))
            ->assertOk()
            ->assertSee('Bagikan Profil UMKM')
            ->assertSee(route('qr.umkm.svg', $umkm->slug), false)
            ->assertSee(route('qr.umkm.open', $umkm->slug), false)
            ->assertSee('Download QR');
    }

    public function test_qr_scan_is_visible_to_umkm_owner_scope(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner QR',
            'email' => 'owner-qr@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);
        $otherOwner = User::query()->create([
            'name' => 'Owner Lain',
            'email' => 'owner-lain-qr@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);
        $ownedUmkm = $this->umkm(['slug' => 'qr-owned', 'user_id' => $owner->id]);
        $otherUmkm = $this->umkm(['slug' => 'qr-other', 'user_id' => $otherOwner->id]);

        $ownedLead = LeadEvent::query()->create([
            'umkm_id' => $ownedUmkm->id,
            'type' => 'qr_scan',
            'source' => 'qr_profile',
            'target_url' => route('umkm.show', $ownedUmkm->slug),
        ]);
        LeadEvent::query()->create([
            'umkm_id' => $otherUmkm->id,
            'type' => 'qr_scan',
            'source' => 'qr_profile',
            'target_url' => route('umkm.show', $otherUmkm->slug),
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
