<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterOwner;
use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_filament_owner_registration_page(): void
    {
        $this->get('/admin/register')
            ->assertOk()
            ->assertSee('Buat akun owner UMKM');
    }

    public function test_owner_registration_creates_umkm_owner_user(): void
    {
        Livewire::test(RegisterOwner::class)
            ->fillForm([
                'name' => 'Owner Baru',
                'email' => 'owner-baru@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect(UmkmResource::getUrl('create'));

        $owner = User::query()->where('email', 'owner-baru@example.test')->firstOrFail();

        $this->assertSame('umkm_owner', $owner->role);
        $this->assertAuthenticatedAs($owner);
    }

    public function test_new_owner_can_access_admin_panel(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Cimuning',
            'email' => 'owner-cimuning@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $this->actingAs($owner)
            ->get('/admin')
            ->assertOk();
    }

    public function test_owner_created_umkm_defaults_to_pending_and_inactive(): void
    {
        $category = $this->category();
        $admin = User::query()->create([
            'name' => 'Admin Cimuning',
            'email' => 'admin-owner-create@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);
        $owner = User::query()->create([
            'name' => 'Owner Baru',
            'email' => 'owner-create@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $this->actingAs($owner);

        Livewire::test(CreateUmkm::class)
            ->fillForm([
                'category_id' => $category->id,
                'name' => 'Dapur Owner Baru',
                'slug' => 'dapur-owner-baru',
                'description' => 'Makanan rumahan untuk warga Cimuning.',
                'owner_name' => 'Owner Baru',
                'whatsapp' => '081234567890',
                'address' => 'Jl. Cimuning Raya',
                'is_active' => true,
                'status' => 'verified',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $umkm = Umkm::query()->where('slug', 'dapur-owner-baru')->firstOrFail();

        $this->assertSame($owner->id, $umkm->user_id);
        $this->assertSame('pending', $umkm->status);
        $this->assertFalse($umkm->is_active);
        $this->assertSame('Pendaftaran UMKM baru', $admin->notifications()->first()?->data['title']);
    }

    public function test_homepage_only_shows_products_from_verified_active_umkms(): void
    {
        $category = $this->category();
        $verifiedUmkm = $this->umkm('dapur-verified', [
            'category_id' => $category->id,
            'status' => 'verified',
            'is_active' => true,
        ]);
        $pendingUmkm = $this->umkm('dapur-pending-home', [
            'category_id' => $category->id,
            'status' => 'pending',
            'is_active' => false,
        ]);

        Product::query()->create([
            'umkm_id' => $verifiedUmkm->id,
            'category_id' => $category->id,
            'name' => 'Produk Terverifikasi',
            'slug' => 'produk-terverifikasi',
            'is_active' => true,
        ]);
        Product::query()->create([
            'umkm_id' => $pendingUmkm->id,
            'category_id' => $category->id,
            'name' => 'Produk Pending',
            'slug' => 'produk-pending',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Produk Terverifikasi')
            ->assertDontSee('Produk Pending');
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

    private function umkm(string $slug, array $attributes = []): Umkm
    {
        return Umkm::query()->create(array_merge([
            'category_id' => $this->category()->id,
            'name' => str($slug)->replace('-', ' ')->title()->toString(),
            'slug' => $slug,
            'status' => 'verified',
            'is_active' => true,
        ], $attributes));
    }
}
