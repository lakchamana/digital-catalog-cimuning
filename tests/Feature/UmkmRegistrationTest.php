<?php

namespace Tests\Feature;

use App\Livewire\Public\UmkmRegistrationForm;
use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class UmkmRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_umkm_registration_page(): void
    {
        Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->get('/daftar-umkm')
            ->assertOk()
            ->assertSee('Daftarkan UMKM Cimuning');
    }

    public function test_guest_can_submit_valid_registration_as_pending_inactive_umkm(): void
    {
        Storage::fake('public');

        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(UmkmRegistrationForm::class)
            ->set('name', 'Dapur Uji Cimuning')
            ->set('category_id', (string) $category->id)
            ->set('description', 'Menyediakan makanan rumahan untuk warga Cimuning dan sekitarnya.')
            ->set('owner_name', 'Ibu Uji')
            ->set('whatsapp', '081234567890')
            ->set('email', 'uji@example.test')
            ->set('address', 'Jl. Cimuning Raya, Mustikajaya')
            ->set('rw', 'RW 02')
            ->set('service_delivery', true)
            ->set('service_cod', true)
            ->set('logo', UploadedFile::fake()->createWithContent('logo.png', $this->tinyPng()))
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $umkm = Umkm::query()->where('name', 'Dapur Uji Cimuning')->firstOrFail();

        $this->assertSame('pending', $umkm->status);
        $this->assertFalse($umkm->is_active);
        $this->assertNull($umkm->user_id);
        $this->assertSame('dapur-uji-cimuning', $umkm->slug);
        $this->assertNotNull($umkm->logo_image);
        Storage::disk('public')->assertExists($umkm->logo_image);
    }

    public function test_registration_generates_unique_slug_for_duplicate_umkm_name(): void
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Sama',
            'slug' => 'dapur-sama',
            'status' => 'verified',
            'is_active' => true,
        ]);

        Livewire::test(UmkmRegistrationForm::class)
            ->set('name', 'Dapur Sama')
            ->set('category_id', (string) $category->id)
            ->set('description', 'Menyediakan makanan rumahan untuk warga Cimuning dan sekitarnya.')
            ->set('owner_name', 'Ibu Sama')
            ->set('whatsapp', '081234567891')
            ->set('address', 'Jl. Cimuning Raya')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('umkms', [
            'name' => 'Dapur Sama',
            'slug' => 'dapur-sama-2',
            'status' => 'pending',
            'is_active' => false,
        ]);
    }

    public function test_registration_rejects_invalid_upload(): void
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::test(UmkmRegistrationForm::class)
            ->set('name', 'Dapur Upload')
            ->set('category_id', (string) $category->id)
            ->set('description', 'Menyediakan makanan rumahan untuk warga Cimuning dan sekitarnya.')
            ->set('owner_name', 'Ibu Upload')
            ->set('whatsapp', '081234567892')
            ->set('address', 'Jl. Cimuning Raya')
            ->set('logo', UploadedFile::fake()->create('dokumen.pdf', 20, 'application/pdf'))
            ->call('submit')
            ->assertHasErrors(['logo']);
    }

    public function test_pending_umkm_is_not_visible_publicly(): void
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Umkm::query()->create([
            'category_id' => $category->id,
            'name' => 'Dapur Pending',
            'slug' => 'dapur-pending',
            'status' => 'pending',
            'is_active' => false,
        ]);

        $this->get('/umkm/dapur-pending')->assertNotFound();
        $this->get('/umkm')
            ->assertOk()
            ->assertDontSee('Dapur Pending');
    }

    private function tinyPng(): string
    {
        return base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=');
    }
}
