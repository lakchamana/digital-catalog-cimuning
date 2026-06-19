<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterOwner;
use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use App\Support\OwnerFormHelper;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
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
            ->assertSee('Buat akun UMKM')
            ->assertSee('Verifikasi keamanan')
            ->assertSee('Masukkan hasil perhitungan di atas.')
            ->assertDontSee('id="form.profile_confirmation" type="text"', false);

        Livewire::test(RegisterOwner::class)
            ->assertFormFieldExists('profile_confirmation', fn ($field): bool => $field instanceof Hidden);
    }

    public function test_owner_registration_creates_umkm_owner_user(): void
    {
        $component = Livewire::test(RegisterOwner::class);
        $captchaToken = session('owner_register_captcha_token');
        $captchaAnswer = session("owner_register_captchas.{$captchaToken}.answer");

        $component
            ->fillForm([
                'name' => 'Owner Baru',
                'email' => 'owner-baru@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'captcha_token' => $captchaToken,
                'captcha_answer' => " {$captchaAnswer} ",
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect(UmkmResource::getUrl('create'));

        $owner = User::query()->where('email', 'owner-baru@example.test')->firstOrFail();

        $this->assertSame('umkm_owner', $owner->role);
        $this->assertAuthenticatedAs($owner);
    }

    public function test_owner_registration_captcha_allows_multiple_open_tabs(): void
    {
        $firstTab = Livewire::test(RegisterOwner::class);
        $firstToken = session('owner_register_captcha_token');
        $firstAnswer = session("owner_register_captchas.{$firstToken}.answer");

        Livewire::test(RegisterOwner::class);

        $firstTab
            ->fillForm([
                'name' => 'Owner Multi Tab',
                'email' => 'owner-multitab@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'captcha_token' => $firstToken,
                'captcha_answer' => $firstAnswer,
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect(UmkmResource::getUrl('create'));

        $this->assertDatabaseHas('users', [
            'email' => 'owner-multitab@example.test',
            'role' => 'umkm_owner',
        ]);
    }

    public function test_owner_registration_rejects_wrong_captcha_and_honeypot(): void
    {
        $wrongComponent = Livewire::test(RegisterOwner::class);
        $wrongToken = session('owner_register_captcha_token');

        $wrongComponent
            ->fillForm([
                'name' => 'Owner Salah',
                'email' => 'owner-salah@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'captcha_token' => $wrongToken,
                'captcha_answer' => '999',
            ])
            ->call('register')
            ->assertHasFormErrors(['captcha_answer']);

        $this->assertDatabaseMissing('users', ['email' => 'owner-salah@example.test']);

        $component = Livewire::test(RegisterOwner::class);
        $captchaToken = session('owner_register_captcha_token');
        $captchaAnswer = session("owner_register_captchas.{$captchaToken}.answer");

        $component
            ->fillForm([
                'name' => 'Owner Bot',
                'email' => 'owner-bot@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'captcha_token' => $captchaToken,
                'captcha_answer' => $captchaAnswer,
                'profile_confirmation' => 'filled-by-bot',
            ])
            ->call('register')
            ->assertHasFormErrors(['captcha_answer']);

        $this->assertDatabaseMissing('users', ['email' => 'owner-bot@example.test']);
    }

    public function test_owner_registration_rejects_missing_captcha_token(): void
    {
        $captchaToken = session('owner_register_captcha_token');
        $captchaAnswer = session("owner_register_captchas.{$captchaToken}.answer");

        Livewire::test(RegisterOwner::class)
            ->fillForm([
                'name' => 'Owner Token Hilang',
                'email' => 'owner-token-hilang@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'captcha_token' => 'token-tidak-ada',
                'captcha_answer' => $captchaAnswer,
            ])
            ->call('register')
            ->assertHasFormErrors(['captcha_answer']);

        $this->assertDatabaseMissing('users', ['email' => 'owner-token-hilang@example.test']);
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
                'description' => 'Makanan rumahan untuk warga Cimuning.',
                'owner_name' => 'Owner Baru',
                'whatsapp' => '081234567890',
                'rw' => 'RW 07',
                'address' => 'Jl. Cimuning Raya',
                'maps_link' => 'https://www.google.com/maps/place/Cimuning/@-6.3123456,107.0123456,17z',
                'instagram' => '@dapur_owner',
                'tiktok' => 'dapurowner',
                'is_active' => true,
                'status' => 'verified',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $umkm = Umkm::query()->where('slug', 'dapur-owner-baru')->firstOrFail();

        $this->assertSame($owner->id, $umkm->user_id);
        $this->assertSame('dapur-owner-baru', $umkm->slug);
        $this->assertSame('pending', $umkm->status);
        $this->assertFalse($umkm->is_active);
        $this->assertSame('-6.3123456', (string) $umkm->latitude);
        $this->assertSame('107.0123456', (string) $umkm->longitude);
        $this->assertSame('https://instagram.com/dapur_owner', $umkm->instagram);
        $this->assertSame('https://www.tiktok.com/@dapurowner', $umkm->tiktok);
        $this->assertSame('Pendaftaran UMKM baru', $admin->notifications()->first()?->data['title']);
    }

    public function test_owner_created_umkm_gets_unique_slug_when_name_is_duplicated(): void
    {
        $category = $this->category();
        $owner = User::query()->create([
            'name' => 'Owner Slug',
            'email' => 'owner-slug@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Sama',
            'slug' => 'dapur-sama',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $this->actingAs($owner);

        Livewire::test(CreateUmkm::class)
            ->fillForm([
                'category_id' => $category->id,
                'name' => 'Dapur Sama',
                'description' => 'Makanan rumahan untuk warga Cimuning.',
                'owner_name' => 'Owner Slug',
                'whatsapp' => '081234567890',
                'rw' => 'RW 08',
                'address' => 'Jl. Cimuning Indah',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('umkms', [
            'user_id' => $owner->id,
            'name' => 'Dapur Sama',
            'slug' => 'dapur-sama-2',
            'status' => 'pending',
            'is_active' => false,
        ]);
    }

    public function test_owner_rw_field_is_searchable_required_and_limited_to_26_cimuning_rws(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner RW',
            'email' => 'owner-rw@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $this->actingAs($owner);

        Livewire::test(CreateUmkm::class)
            ->assertFormFieldExists('rw', function ($field): bool {
                return $field instanceof Select
                    && $field->isSearchable()
                    && ! $field->isNative()
                    && $field->isRequired()
                    && $field->getOptions() === Umkm::rwOptions();
            })
            ->assertFormFieldDoesNotExist('view_count');

        $this->assertCount(26, Umkm::rwOptions());
        $this->assertSame('RW 01', Umkm::rwOptions()['RW 01']);
        $this->assertSame('RW 26', Umkm::rwOptions()['RW 26']);
    }

    public function test_owner_cannot_submit_rw_outside_cimuning_options(): void
    {
        $category = $this->category();
        $owner = User::query()->create([
            'name' => 'Owner RW Salah',
            'email' => 'owner-rw-salah@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $this->actingAs($owner);

        Livewire::test(CreateUmkm::class)
            ->fillForm([
                'category_id' => $category->id,
                'name' => 'Usaha RW Salah',
                'description' => 'Usaha lokal Cimuning.',
                'owner_name' => 'Owner RW Salah',
                'whatsapp' => '081234567890',
                'rw' => 'RW 27',
                'address' => 'Jl. Cimuning Raya',
            ])
            ->call('create')
            ->assertHasFormErrors(['rw']);

        $this->assertDatabaseMissing('umkms', ['name' => 'Usaha RW Salah']);
    }

    public function test_owner_form_hides_technical_fields_and_admin_cannot_open_owner_form(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Form',
            'email' => 'owner-form@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);
        $admin = User::query()->create([
            'name' => 'Admin Form',
            'email' => 'admin-form@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($owner);

        Livewire::test(CreateUmkm::class)
            ->assertDontSee('Slug URL publik')
            ->assertDontSee('Latitude')
            ->assertDontSee('Longitude')
            ->assertDontSee('Pengaturan publik')
            ->assertDontSee('Jumlah dilihat')
            ->assertSee('Pastikan semua informasi sudah benar.');

        $this->actingAs($admin);

        $this->get(UmkmResource::getUrl('create'))->assertForbidden();
    }

    public function test_maps_text_parser_accepts_coordinates_from_google_maps_text(): void
    {
        $coordinates = OwnerFormHelper::coordinatesFromMapsText('https://www.google.com/maps/place/Cimuning/@-6.3123456,107.0123456,17z');

        $this->assertSame([
            'latitude' => '-6.3123456',
            'longitude' => '107.0123456',
        ], $coordinates);
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
