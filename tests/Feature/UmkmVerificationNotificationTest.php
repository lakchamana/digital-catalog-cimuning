<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Umkm;
use App\Models\User;
use App\Support\UmkmVerificationWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verify_updates_umkm_and_notifies_owner(): void
    {
        [$owner, $umkm] = $this->ownerAndUmkm('pending', false);

        UmkmVerificationWorkflow::verify($umkm);

        $this->assertSame('verified', $umkm->refresh()->status);
        $this->assertTrue($umkm->is_active);
        $this->assertSame('UMKM terverifikasi', $owner->notifications()->first()?->data['title']);
    }

    public function test_request_revision_updates_umkm_and_notifies_owner(): void
    {
        [$owner, $umkm] = $this->ownerAndUmkm('pending', true);

        UmkmVerificationWorkflow::requestRevision($umkm);

        $this->assertSame('need_revision', $umkm->refresh()->status);
        $this->assertFalse($umkm->is_active);
        $this->assertSame('Profil UMKM perlu revisi', $owner->notifications()->first()?->data['title']);
    }

    public function test_reject_updates_umkm_and_notifies_owner(): void
    {
        [$owner, $umkm] = $this->ownerAndUmkm('pending', true);

        UmkmVerificationWorkflow::reject($umkm);

        $this->assertSame('rejected', $umkm->refresh()->status);
        $this->assertFalse($umkm->is_active);
        $this->assertSame('Pendaftaran UMKM ditolak', $owner->notifications()->first()?->data['title']);
    }

    public function test_status_update_without_owner_does_not_fail(): void
    {
        $category = $this->category();
        $umkm = Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Tanpa Owner',
            'slug' => 'dapur-tanpa-owner',
            'status' => 'pending',
            'is_active' => false,
        ]);

        UmkmVerificationWorkflow::verify($umkm);
        UmkmVerificationWorkflow::requestRevision($umkm);
        UmkmVerificationWorkflow::reject($umkm);

        $this->assertSame('rejected', $umkm->refresh()->status);
        $this->assertFalse($umkm->is_active);
        $this->assertDatabaseCount('notifications', 0);
    }

    /**
     * @return array{0: User, 1: Umkm}
     */
    private function ownerAndUmkm(string $status, bool $isActive): array
    {
        $owner = User::query()->create([
            'name' => 'Owner Cimuning',
            'email' => uniqid('owner-', true).'@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $category = $this->category();

        $umkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Owner',
            'slug' => uniqid('dapur-owner-', false),
            'status' => $status,
            'is_active' => $isActive,
        ]);

        return [$owner, $umkm];
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
