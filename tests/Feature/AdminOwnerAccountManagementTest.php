<?php

namespace Tests\Feature;

use App\Filament\Resources\OwnerAccounts\OwnerAccountResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Umkm;
use App\Models\UmkmContact;
use App\Models\UmkmSocialLink;
use App\Models\UmkmSubmission;
use App\Models\User;
use App\Support\ContentModeration;
use App\Support\OwnerAccountAdministration;
use Filament\PanelRegistry;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminOwnerAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_owner_account_resource_without_password_or_mutation_pages(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($admin)->get('/admin/owner-accounts')->assertOk();

        $this->assertArrayNotHasKey('create', OwnerAccountResource::getPages());
        $this->assertArrayNotHasKey('edit', OwnerAccountResource::getPages());
        $this->assertArrayNotHasKey('password', $owner->toArray());
        $this->assertArrayNotHasKey('remember_token', $owner->toArray());
    }

    public function test_owner_can_open_own_profile_but_cannot_open_owner_account_resource(): void
    {
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($owner)->get('/admin/profile')->assertOk();
        $this->get('/admin/owner-accounts')->assertForbidden();
    }

    public function test_owner_resource_excludes_admin_accounts(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($admin);

        $this->assertSame([$owner->id], OwnerAccountResource::getEloquentQuery()->pluck('id')->all());
    }

    public function test_suspend_revokes_access_and_reactivation_restores_it(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');

        DB::table('sessions')->insert([
            'id' => 'owner-session',
            'user_id' => $owner->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => 'test',
            'last_activity' => now()->timestamp,
        ]);
        DB::table('password_reset_tokens')->insert([
            'email' => $owner->email,
            'token' => 'hashed-token',
            'created_at' => now(),
        ]);

        OwnerAccountAdministration::suspend($owner, $admin, 'Permintaan pengamanan akun owner.');

        $owner->refresh();
        $this->assertSame('suspended', $owner->account_status);
        $this->assertFalse($owner->canAccessPanel(app(PanelRegistry::class)->get('admin')));
        $this->assertDatabaseMissing('sessions', ['user_id' => $owner->id]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $owner->email]);
        $this->assertDatabaseHas('moderation_actions', [
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'action' => 'owner_suspended',
        ]);

        $this->actingAs($owner)->get('/admin')->assertRedirect('/admin/login');

        OwnerAccountAdministration::reactivate($owner, $admin, 'Identitas owner telah dikonfirmasi.');

        $this->assertSame('active', $owner->refresh()->account_status);
        $this->actingAs($owner)->get('/admin')->assertOk();
    }

    public function test_identity_correction_requires_reason_and_unique_email(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $otherOwner = $this->user('umkm_owner', 'other@example.test');

        try {
            OwnerAccountAdministration::correctIdentity($owner, $admin, 'Nama Baru', $otherOwner->email, 'Koreksi diminta langsung oleh owner.');
            $this->fail('Email duplikat seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertSame('owner@example.test', $owner->refresh()->email);
        }

        OwnerAccountAdministration::correctIdentity($owner, $admin, 'Nama Owner Baru', 'owner-baru@example.test', 'Koreksi diminta langsung oleh owner.');

        $this->assertSame('Nama Owner Baru', $owner->refresh()->name);
        $this->assertSame('owner-baru@example.test', $owner->email);
        $this->assertDatabaseHas('moderation_actions', [
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'action' => 'owner_identity_corrected',
        ]);
    }

    public function test_admin_account_cannot_be_targeted_by_owner_administration(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $otherAdmin = $this->user('admin', 'other-admin@example.test');

        $this->expectException(HttpException::class);
        OwnerAccountAdministration::suspend($otherAdmin, $admin, 'Percobaan yang wajib ditolak sistem.');
    }

    public function test_anonymization_scrubs_personal_data_and_media_but_keeps_minimum_audit(): void
    {
        Storage::fake('public');
        config()->set('filesystems.default', 'public');

        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $category = Category::query()->create(['name' => 'Kuliner', 'slug' => 'kuliner', 'is_active' => true]);
        $umkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Owner',
            'slug' => 'dapur-owner',
            'owner_name' => 'Nama Pribadi',
            'whatsapp' => '081234567890',
            'address' => 'Alamat pribadi',
            'logo_image' => 'umkm/logo.jpg',
            'cover_image' => 'umkm/cover.jpg',
            'status' => 'verified',
            'is_active' => true,
        ]);
        $product = Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Produk Owner',
            'slug' => 'produk-owner',
            'image' => 'products/main.jpg',
            'is_active' => true,
        ]);
        ProductImage::query()->create(['product_id' => $product->id, 'path' => 'products/gallery.jpg']);
        UmkmContact::query()->create(['umkm_id' => $umkm->id, 'type' => 'whatsapp', 'value' => '081234567890']);
        UmkmSocialLink::query()->create(['umkm_id' => $umkm->id, 'platform' => 'instagram', 'url' => 'https://instagram.com/owner']);
        $submission = UmkmSubmission::query()->create([
            'umkm_id' => $umkm->id,
            'submitted_by' => $owner->id,
            'type' => 'initial',
            'status' => 'approved',
            'payload' => ['owner_name' => 'Nama Pribadi', 'whatsapp' => '081234567890'],
            'review_notes' => 'Disetujui setelah pemeriksaan.',
            'submitted_at' => now(),
        ]);

        // cover.jpg sengaja tidak dibuat untuk memastikan media lama yang sudah hilang tidak memacetkan anonimisasi.
        foreach (['umkm/logo.jpg', 'products/main.jpg', 'products/gallery.jpg'] as $path) {
            Storage::disk('public')->put($path, 'image');
        }

        OwnerAccountAdministration::suspend($owner, $admin, 'Owner meminta penghapusan data akun.');
        OwnerAccountAdministration::anonymize($owner, $admin, 'ANONIMKAN', 'Permintaan penghapusan owner telah diverifikasi.');

        $owner->refresh();
        $umkm->refresh();
        $product->refresh();
        $submission->refresh();

        $this->assertSame('anonymized', $owner->account_status);
        $this->assertStringStartsWith('deleted-', $owner->email);
        $this->assertNull($umkm->whatsapp);
        $this->assertNull($umkm->address);
        $this->assertFalse($umkm->is_active);
        $this->assertTrue($umkm->is_admin_blocked);
        $this->assertFalse($product->is_active);
        $this->assertTrue($product->is_admin_blocked);
        $this->assertSame([], $submission->payload);
        $this->assertSame('Disetujui setelah pemeriksaan.', $submission->review_notes);
        $this->assertDatabaseMissing('product_images', ['product_id' => $product->id]);
        $this->assertDatabaseMissing('umkm_contacts', ['umkm_id' => $umkm->id]);
        $this->assertDatabaseMissing('umkm_social_links', ['umkm_id' => $umkm->id]);
        Storage::disk('public')->assertMissing('umkm/logo.jpg');
        Storage::disk('public')->assertMissing('products/gallery.jpg');
        $this->assertDatabaseHas('moderation_actions', ['action' => 'owner_anonymized', 'subject_id' => $owner->id]);
    }

    public function test_password_reset_is_environment_gated_and_never_assigns_a_password(): void
    {
        Notification::fake();
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $passwordHash = $owner->getRawOriginal('password');

        try {
            OwnerAccountAdministration::sendPasswordReset($owner, $admin, 'Owner meminta pemulihan akses akun.');
            $this->fail('Reset password seharusnya nonaktif secara default.');
        } catch (ValidationException) {
            $this->assertSame($passwordHash, $owner->refresh()->getRawOriginal('password'));
        }

        config()->set('auth.password_reset_enabled', true);
        OwnerAccountAdministration::sendPasswordReset($owner, $admin, 'Owner meminta pemulihan akses akun.');

        Notification::assertSentTo($owner, ResetPassword::class);
        $this->assertSame($passwordHash, $owner->refresh()->getRawOriginal('password'));
        $this->assertDatabaseHas('moderation_actions', ['action' => 'owner_password_reset_sent', 'subject_id' => $owner->id]);
    }

    public function test_admin_can_block_umkm_without_editing_owner_content_and_restore_it(): void
    {
        Notification::fake();
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $category = Category::query()->create(['name' => 'Kuliner', 'slug' => 'kuliner', 'is_active' => true]);
        $umkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Aman',
            'slug' => 'dapur-aman',
            'status' => 'verified',
            'is_active' => true,
            'is_featured' => true,
        ]);
        $product = Product::query()->create([
            'umkm_id' => $umkm->id,
            'name' => 'Nasi Aman',
            'slug' => 'nasi-aman',
            'is_active' => true,
        ]);

        ContentModeration::blockUmkm($umkm, $admin, 'Profil perlu dinonaktifkan untuk pemeriksaan.');

        $this->assertTrue($umkm->refresh()->is_admin_blocked);
        $this->assertFalse($umkm->is_featured);
        $this->assertSame('Dapur Aman', $umkm->name);
        $this->assertFalse(Umkm::query()->publiclyVisible()->whereKey($umkm)->exists());
        $this->assertFalse(Product::query()->publiclyVisible()->whereKey($product)->exists());
        $this->get('/umkm/dapur-aman')->assertNotFound();
        $this->get('/produk/nasi-aman')->assertNotFound();
        $this->get('/qr/umkm/dapur-aman.svg')->assertNotFound();
        $this->get('/sitemap.xml')->assertDontSee('/umkm/dapur-aman', false)->assertDontSee('/produk/nasi-aman', false);

        ContentModeration::unblockUmkm($umkm, $admin, 'Pemeriksaan selesai dan profil dapat dipulihkan.');

        $this->assertFalse($umkm->refresh()->is_admin_blocked);
        $this->get('/umkm/dapur-aman')->assertOk();
        $this->get('/produk/nasi-aman')->assertOk();
    }

    public function test_used_categories_cannot_be_deleted_but_unused_categories_can(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $used = Category::query()->create(['name' => 'Kuliner', 'slug' => 'kuliner', 'is_active' => true]);
        $unused = Category::query()->create(['name' => 'Lainnya', 'slug' => 'lainnya', 'is_active' => true]);
        Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $used->id,
            'name' => 'Dapur Owner',
            'slug' => 'dapur-owner',
        ]);

        $this->assertFalse(Gate::forUser($admin)->allows('delete', $used));
        $this->assertTrue(Gate::forUser($admin)->allows('delete', $unused));
        $this->assertFalse(Gate::forUser($admin)->allows('deleteAny', Category::class));
    }

    private function user(string $role, string $email): User
    {
        return User::query()->create([
            'name' => ucfirst(str_replace('_', ' ', $role)),
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }
}
