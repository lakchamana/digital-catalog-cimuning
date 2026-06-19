<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\UmkmSubmissions\UmkmSubmissionResource;
use App\Filament\Resources\Umkms\Pages\EditUmkm;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Support\ContentModeration;
use App\Support\UmkmSubmissionWorkflow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Tests\TestCase;

class ProfessionalUmkmVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_submission_is_audited_and_approval_publishes_umkm(): void
    {
        [$owner, $admin, $umkm] = $this->peopleAndUmkm('pending', false);

        $submission = UmkmSubmissionWorkflow::submit($umkm, $owner, [
            ...UmkmSubmissionWorkflow::payloadFromUmkm($umkm),
            'name' => 'Dapur Siap Review',
        ]);

        $this->assertSame('pending', $submission->status);
        $this->assertSame('initial', $submission->type);
        $this->assertFalse($umkm->refresh()->is_active);
        $this->assertDatabaseCount('notifications', 1);

        UmkmSubmissionWorkflow::approve($submission, $admin, 'Data telah diperiksa.', [
            'data_complete' => true,
            'contact_valid' => true,
            'content_appropriate' => true,
        ]);

        $this->assertSame('verified', $umkm->refresh()->status);
        $this->assertTrue($umkm->is_active);
        $this->assertSame('Dapur Siap Review', $umkm->name);
        $this->assertSame('approved', $submission->refresh()->status);
        $this->assertSame($admin->id, $submission->reviewed_by);
        $this->assertNotNull($submission->reviewed_at);
        $this->assertSame('Pengajuan UMKM disetujui', $owner->notifications()->latest()->first()?->data['title']);
    }

    public function test_verified_profile_changes_stay_in_draft_until_approved(): void
    {
        [$owner, $admin, $umkm] = $this->peopleAndUmkm('verified', true);
        $umkm->update(['name' => 'Nama Publik Lama', 'slug' => 'url-tetap', 'is_featured' => true]);

        $submission = UmkmSubmissionWorkflow::submit($umkm, $owner, [
            ...UmkmSubmissionWorkflow::payloadFromUmkm($umkm),
            'name' => 'Nama Baru Diajukan',
            'address' => 'Alamat baru yang diajukan',
            'slug' => 'slug-tidak-boleh-masuk',
            'is_featured' => false,
            'status' => 'rejected',
        ]);

        $this->assertSame('update', $submission->type);
        $this->assertArrayNotHasKey('slug', $submission->payload);
        $this->assertArrayNotHasKey('is_featured', $submission->payload);
        $this->assertSame('Nama Publik Lama', $umkm->refresh()->name);
        $this->assertSame('url-tetap', $umkm->slug);
        $this->assertTrue($umkm->is_featured);

        UmkmSubmissionWorkflow::approve($submission, $admin, null, [
            'data_complete' => true,
            'contact_valid' => true,
            'content_appropriate' => true,
        ]);

        $this->assertSame('Nama Baru Diajukan', $umkm->refresh()->name);
        $this->assertSame('url-tetap', $umkm->slug);
        $this->assertTrue($umkm->is_featured);
        $this->assertSame($owner->id, $umkm->user_id);
    }

    public function test_owner_edit_page_submits_draft_without_mutating_verified_profile(): void
    {
        [$owner, , $umkm] = $this->peopleAndUmkm('verified', true);
        $originalName = $umkm->name;

        Livewire::actingAs($owner)
            ->test(EditUmkm::class, ['record' => $umkm->getRouteKey()])
            ->fillForm(['name' => 'Nama Draft dari Form Owner'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($originalName, $umkm->refresh()->name);
        $this->assertSame('pending', $umkm->latestSubmission?->status);
        $this->assertSame('update', $umkm->latestSubmission?->type);
        $this->assertSame('Nama Draft dari Form Owner', $umkm->latestSubmission?->payload['name']);
    }

    public function test_revision_and_rejection_require_reason_and_owner_can_resubmit(): void
    {
        [$owner, $admin, $umkm] = $this->peopleAndUmkm('pending', false);
        $submission = UmkmSubmissionWorkflow::submit($umkm, $owner, UmkmSubmissionWorkflow::payloadFromUmkm($umkm));

        try {
            UmkmSubmissionWorkflow::requestRevision($submission, $admin, 'pendek');
            $this->fail('Alasan pendek seharusnya ditolak.');
        } catch (ValidationException) {
            $this->assertSame('pending', $submission->refresh()->status);
        }

        UmkmSubmissionWorkflow::requestRevision($submission, $admin, 'Mohon lengkapi alamat dan nomor WhatsApp usaha.');
        $this->assertSame('need_revision', $submission->refresh()->status);
        $this->assertSame('need_revision', $umkm->refresh()->status);

        $resubmission = UmkmSubmissionWorkflow::submit($umkm, $owner, [
            ...$submission->payload,
            'address' => 'Jl. Cimuning Raya Nomor 10',
        ]);

        $this->assertSame('pending', $resubmission->status);
        $this->assertSame('Jl. Cimuning Raya Nomor 10', $resubmission->payload['address']);

        $this->expectException(ValidationException::class);
        UmkmSubmissionWorkflow::reject($submission, $admin, 'Pengajuan lama tidak boleh diproses untuk kedua kali.');
    }

    public function test_admin_is_read_only_for_owner_content_but_can_view_review_page(): void
    {
        [$owner, $admin, $umkm] = $this->peopleAndUmkm('pending', false);
        $submission = UmkmSubmissionWorkflow::submit($umkm, $owner, UmkmSubmissionWorkflow::payloadFromUmkm($umkm));
        $product = $this->product($umkm);

        $this->actingAs($admin);
        $this->get(UmkmResource::getUrl('edit', ['record' => $umkm]))->assertForbidden();
        $this->get(UmkmResource::getUrl('create'))->assertForbidden();
        $this->get(UmkmResource::getUrl('view', ['record' => $umkm]))
            ->assertOk()
            ->assertSee('Data milik owner ditampilkan read-only');
        $this->get(ProductResource::getUrl('edit', ['record' => $product]))->assertForbidden();
        $this->get(ProductResource::getUrl('create'))->assertForbidden();
        $this->get(UmkmSubmissionResource::getUrl('view', ['record' => $submission]))
            ->assertOk()
            ->assertSee('Data yang diajukan owner')
            ->assertSee('Data owner tidak dapat diedit');

        $this->assertTrue(Gate::forUser($owner)->denies('view', $submission));
    }

    public function test_product_blocking_is_audited_and_excludes_product_from_public_pages(): void
    {
        [$owner, $admin, $umkm] = $this->peopleAndUmkm('verified', true);
        $product = $this->product($umkm);

        $this->get(route('products.index'))->assertOk()->assertSee($product->name);

        ContentModeration::blockProduct($product, $admin, 'Foto dan deskripsi produk perlu diperbaiki sebelum tayang.');

        $this->assertTrue($product->refresh()->is_admin_blocked);
        $this->assertDatabaseHas('moderation_actions', [
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'action' => 'blocked',
        ]);
        $this->get(route('products.index'))->assertOk()->assertDontSee($product->name);
        $this->get(route('home'))->assertOk()->assertDontSee($product->name);
        $this->get(route('umkm.show', $umkm->slug))->assertOk()->assertDontSee($product->name);

        ContentModeration::unblockProduct($product, $admin, 'Perbaikan produk sudah ditinjau dan dapat ditampilkan kembali.');
        $this->assertFalse($product->refresh()->is_admin_blocked);
    }

    public function test_featured_curation_is_separate_and_audited(): void
    {
        [, $admin, $umkm] = $this->peopleAndUmkm('verified', true);

        ContentModeration::setFeatured($umkm, $admin, true);

        $this->assertTrue($umkm->refresh()->is_featured);
        $this->assertDatabaseHas('moderation_actions', [
            'subject_type' => Umkm::class,
            'subject_id' => $umkm->id,
            'action' => 'featured',
        ]);
    }

    private function peopleAndUmkm(string $status, bool $active): array
    {
        $owner = User::query()->create([
            'name' => 'Owner Profesional',
            'email' => uniqid('owner-', true).'@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);
        $admin = User::query()->create([
            'name' => 'Admin Reviewer',
            'email' => uniqid('admin-', true).'@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $category = Category::query()->firstOrCreate(
            ['slug' => 'kuliner'],
            ['name' => 'Kuliner', 'is_active' => true, 'sort_order' => 1],
        );
        $umkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Profesional',
            'slug' => uniqid('dapur-profesional-', false),
            'description' => 'Usaha kuliner lokal Cimuning.',
            'owner_name' => $owner->name,
            'whatsapp' => '081234567890',
            'rw' => 'RW 01',
            'address' => 'Cimuning, Kota Bekasi',
            'status' => $status,
            'is_active' => $active,
        ]);

        return [$owner, $admin, $umkm];
    }

    private function product(Umkm $umkm): Product
    {
        return Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $umkm->category_id,
            'name' => 'Produk Moderasi',
            'slug' => uniqid('produk-moderasi-', false),
            'description' => 'Produk lokal untuk pengujian.',
            'is_active' => true,
        ]);
    }
}
