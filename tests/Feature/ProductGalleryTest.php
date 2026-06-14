<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Umkm;
use App\Models\User;
use App\Support\UploadDisk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductGalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_card_uses_first_gallery_image_when_main_image_is_empty(): void
    {
        [$product] = $this->createPublicProduct();

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'products/gallery/nasi-kuning-utama.jpg',
            'alt_text' => 'Nasi kuning tampak depan',
            'sort_order' => 0,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'products/gallery/nasi-kuning-paket.jpg',
            'alt_text' => 'Paket nasi kuning',
            'sort_order' => 1,
        ]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee(asset('storage/products/gallery/nasi-kuning-utama.jpg'), false)
            ->assertSee('+1 foto');
    }

    public function test_product_images_are_ordered_by_sort_order(): void
    {
        [$product] = $this->createPublicProduct();

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'products/gallery/later.jpg',
            'sort_order' => 20,
        ]);

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'products/gallery/first.jpg',
            'sort_order' => 1,
        ]);

        $this->assertSame('products/gallery/first.jpg', $product->refresh()->images->first()?->path);
    }

    public function test_upload_disk_uses_cloudinary_when_configured_and_public_for_local_default(): void
    {
        config(['filesystems.default' => 'cloudinary']);
        $this->assertSame('cloudinary', UploadDisk::name());

        config(['filesystems.default' => 'local']);
        $this->assertSame('public', UploadDisk::name());
    }

    /**
     * @return array{0: Product, 1: Umkm}
     */
    private function createPublicProduct(): array
    {
        $category = Category::query()->create([
            'name' => 'Kuliner',
            'slug' => 'kuliner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $owner = User::query()->create([
            'name' => 'Owner Produk',
            'email' => 'owner-produk@example.test',
            'password' => 'password',
            'role' => 'umkm_owner',
        ]);

        $umkm = Umkm::query()->create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Dapur Galeri',
            'slug' => 'dapur-galeri',
            'status' => 'verified',
            'is_active' => true,
            'whatsapp' => '081234567890',
        ]);

        $product = Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Kuning Galeri',
            'slug' => 'nasi-kuning-galeri',
            'description' => 'Nasi kuning rumahan.',
            'is_active' => true,
        ]);

        return [$product, $umkm];
    }
}
