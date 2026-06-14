<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UmkmRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_account_first_umkm_registration_page(): void
    {
        $this->get('/daftar-umkm')
            ->assertOk()
            ->assertSee('Kelola profil UMKM lewat akun owner')
            ->assertSee('Buat Akun Owner')
            ->assertSee('Sudah punya akun? Masuk')
            ->assertDontSeeLivewire('public.umkm-registration-form');
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
}
