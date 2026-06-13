<?php

namespace Tests\Feature;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_filament_panel(): void
    {
        $admin = User::query()->create([
            'name' => 'Admin Cimuning',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_umkm_owner_cannot_access_category_master_data(): void
    {
        $owner = User::query()->create([
            'name' => 'Owner Cimuning',
            'email' => 'owner@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $this->actingAs($owner)
            ->get('/admin/categories')
            ->assertForbidden();
    }

    public function test_product_resource_query_is_scoped_to_owner_umkm(): void
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Satu',
            'email' => 'owner-satu@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $otherOwner = User::query()->create([
            'name' => 'Owner Dua',
            'email' => 'owner-dua@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $ownedUmkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Sendiri',
            'slug' => 'dapur-sendiri',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $otherUmkm = Umkm::query()->create([
            'user_id' => $otherOwner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Tetangga',
            'slug' => 'dapur-tetangga',
            'status' => 'verified',
            'is_active' => true,
        ]);

        $ownedProduct = Product::query()->create([
            'umkm_id' => $ownedUmkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Sendiri',
            'slug' => 'nasi-sendiri',
            'is_active' => true,
        ]);

        Product::query()->create([
            'umkm_id' => $otherUmkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Tetangga',
            'slug' => 'nasi-tetangga',
            'is_active' => true,
        ]);

        $this->actingAs($owner);

        $this->assertSame(
            [$ownedProduct->id],
            ProductResource::getEloquentQuery()->pluck('id')->all(),
        );
    }
}
