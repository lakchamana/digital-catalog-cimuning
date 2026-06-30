<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Widgets\OwnerOverviewStats;
use App\Filament\Widgets\OwnerQuickActions;
use App\Filament\Widgets\PlatformStats;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\UmkmSubmission;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class OwnerDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_platform_and_owner_widgets_are_separated_by_role(): void
    {
        $admin = $this->user('admin', 'admin@example.test');
        $owner = $this->user('umkm_owner', 'owner@example.test');

        $this->actingAs($admin);
        $this->assertTrue(PlatformStats::canView());
        $this->assertFalse(OwnerOverviewStats::canView());
        $this->assertFalse(OwnerQuickActions::canView());

        $this->actingAs($owner);
        $this->assertFalse(PlatformStats::canView());
        $this->assertTrue(OwnerOverviewStats::canView());
        $this->assertTrue(OwnerQuickActions::canView());

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Status Profil')
            ->assertSee('Belum lengkap')
            ->assertSee('Produk/Jasa')
            ->assertSee('Tampil Publik')
            ->assertSee('Perlu Tindakan')
            ->assertSeeHtml('data-owner-stats-compact')
            ->assertDontSee('Kategori aktif');
    }

    public function test_owner_overview_uses_only_owned_products_and_actionable_states(): void
    {
        $category = $this->category();
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $otherOwner = $this->user('umkm_owner', 'other@example.test');
        $umkm = $this->umkm($owner, $category, [
            'name' => 'Dapur Owner',
            'slug' => 'dapur-owner',
            'status' => 'verified',
            'is_active' => true,
        ]);
        $otherUmkm = $this->umkm($otherOwner, $category, [
            'name' => 'Dapur Lain',
            'slug' => 'dapur-lain',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $this->product($umkm, 'Produk Publik', ['is_active' => true]);
        $this->product($umkm, 'Produk Diblokir', [
            'is_active' => true,
            'is_admin_blocked' => true,
        ]);
        $this->product($otherUmkm, 'Produk Owner Lain', ['is_active' => true]);

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Terverifikasi')
            ->assertSee('Kelola katalog usaha Anda')
            ->assertSee('Produk yang dapat ditemukan masyarakat')
            ->assertSee('Buka dan selesaikan catatan yang tersedia');
    }

    public function test_owner_profile_status_follows_review_and_blocking_state(): void
    {
        $category = $this->category();
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $umkm = $this->umkm($owner, $category, [
            'status' => 'pending',
            'is_active' => false,
        ]);
        $submission = UmkmSubmission::query()->create([
            'umkm_id' => $umkm->id,
            'submitted_by' => $owner->id,
            'type' => 'initial',
            'status' => 'pending',
            'payload' => ['name' => $umkm->name],
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Menunggu review');

        $submission->update(['status' => 'approved', 'reviewed_at' => now()]);
        $umkm->update(['status' => 'verified', 'is_active' => true]);

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Terverifikasi');

        $submission->update(['status' => 'need_revision']);

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Perlu revisi');

        $umkm->update(['is_admin_blocked' => true]);

        Livewire::actingAs($owner)
            ->test(OwnerOverviewStats::class)
            ->assertSee('Dinonaktifkan');
    }

    public function test_owner_quick_actions_link_to_business_catalog_public_profile_and_account_security(): void
    {
        $category = $this->category();
        $owner = $this->user('umkm_owner', 'owner@example.test');
        $umkm = $this->umkm($owner, $category, [
            'status' => 'verified',
            'is_active' => true,
        ]);
        $this->product($umkm, 'Produk Owner', ['is_active' => true]);

        Livewire::actingAs($owner)
            ->test(OwnerQuickActions::class)
            ->assertSee('Kelola Profil UMKM')
            ->assertSee('Kelola Produk/Jasa')
            ->assertSee('Lihat Profil Publik')
            ->assertSee('Keamanan Akun')
            ->assertSee('Hubungi Bantuan')
            ->assertSee('cimuningppk@gmail.com')
            ->assertSee('wa.me/6287804054071', escape: false)
            ->assertSee('Periksa tampilan yang dilihat masyarakat')
            ->assertSeeHtml('data-owner-action-list')
            ->assertSee(route('filament.admin.auth.profile'), escape: false)
            ->assertSee(route('umkm.show', $umkm->slug), escape: false);
    }

    public function test_owner_password_change_requires_the_current_password(): void
    {
        $owner = $this->user('umkm_owner', 'owner@example.test');

        Livewire::actingAs($owner)
            ->test(EditProfile::class)
            ->assertSee('Keamanan Akun')
            ->fillForm([
                'name' => $owner->name,
                'email' => $owner->email,
                'currentPassword' => 'password-salah',
                'password' => 'Password-Baru-123!',
                'passwordConfirmation' => 'Password-Baru-123!',
            ])
            ->call('save')
            ->assertHasFormErrors(['currentPassword']);

        $this->assertTrue(Hash::check('password', $owner->fresh()->password));

        Livewire::actingAs($owner->fresh())
            ->test(EditProfile::class)
            ->fillForm([
                'name' => $owner->name,
                'email' => $owner->email,
                'currentPassword' => 'password',
                'password' => 'Password-Baru-123!',
                'passwordConfirmation' => 'Password-Baru-123!',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('Password-Baru-123!', $owner->fresh()->password));
    }

    public function test_password_reset_is_only_enabled_by_the_environment_flag(): void
    {
        $provider = new AdminPanelProvider(app());

        config()->set('auth.password_reset_enabled', false);
        $this->assertFalse($provider->panel(Panel::make())->hasPasswordReset());

        config()->set('auth.password_reset_enabled', true);
        $this->assertTrue($provider->panel(Panel::make())->hasPasswordReset());
    }

    private function user(string $role, string $email): User
    {
        return User::query()->create([
            'name' => $role === 'admin' ? 'Admin' : 'Owner',
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function category(): Category
    {
        return Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);
    }

    private function umkm(User $owner, Category $category, array $attributes = []): Umkm
    {
        return Umkm::query()->create(array_merge([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'UMKM Owner',
            'slug' => 'umkm-owner',
            'status' => 'pending',
            'is_active' => false,
            'is_admin_blocked' => false,
        ], $attributes));
    }

    private function product(Umkm $umkm, string $name, array $attributes = []): Product
    {
        return Product::query()->create(array_merge([
            'umkm_id' => $umkm->id,
            'category_id' => $umkm->category_id,
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'is_active' => false,
            'is_admin_blocked' => false,
        ], $attributes));
    }
}
