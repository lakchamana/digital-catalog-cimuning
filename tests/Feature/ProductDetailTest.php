<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_product_detail_renders_catalog_information_and_ctas(): void
    {
        [$product, $umkm] = $this->publicProduct();

        ProductImage::query()->create([
            'product_id' => $product->id,
            'path' => 'products/gallery/nasi-detail-1.jpg',
            'alt_text' => 'Nasi detail tampak depan',
            'sort_order' => 1,
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee($product->description)
            ->assertSee('Rp 25.000')
            ->assertSee($umkm->name)
            ->assertSee('Tanya Produk')
            ->assertSee('Lihat Profil UMKM')
            ->assertSee(route('umkm.show', $umkm->slug), false)
            ->assertSee('https://wa.me/6281234567890', false)
            ->assertSee(asset('storage/products/detail/nasi-detail.jpg'), false)
            ->assertSee(asset('storage/products/gallery/nasi-detail-1.jpg'), false)
            ->assertDontSee('Tambah ke keranjang')
            ->assertDontSee('Bayar sekarang');
    }

    public function test_product_detail_only_allows_public_products_from_verified_active_umkms(): void
    {
        [$publicProduct] = $this->publicProduct();
        [$inactiveProduct] = $this->publicProduct(['slug' => 'produk-inactive'], ['is_active' => false]);
        [$blockedProduct] = $this->publicProduct(['slug' => 'produk-blocked'], ['is_admin_blocked' => true]);
        [$pendingProduct] = $this->publicProduct(
            ['slug' => 'produk-pending'],
            [],
            ['slug' => 'umkm-pending-product', 'status' => 'pending', 'is_active' => false],
        );

        $this->get(route('products.show', $publicProduct->slug))->assertOk();
        $this->get(route('products.show', $inactiveProduct->slug))->assertNotFound();
        $this->get(route('products.show', $blockedProduct->slug))->assertNotFound();
        $this->get(route('products.show', $pendingProduct->slug))->assertNotFound();
    }

    public function test_product_detail_renders_seo_metadata_and_product_schema(): void
    {
        [$product] = $this->publicProduct();

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('products.show', $product->slug).'">', false)
            ->assertSee('<meta property="og:type" content="product">', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/products/detail/nasi-detail.jpg').'">', false)
            ->assertSee('<script type="application/ld+json">', false)
            ->assertSee('"@type": "Product"', false)
            ->assertSee('"name": "Nasi Detail"', false)
            ->assertSee('"price": 25000', false);
    }

    public function test_product_cards_link_to_product_detail(): void
    {
        [$product] = $this->publicProduct();

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee(route('products.show', $product->slug), false)
            ->assertSee('Lihat Detail')
            ->assertDontSee('Lihat UMKM');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('products.show', $product->slug), false)
            ->assertSee('Detail Produk');
    }

    /**
     * @return array{0: Product, 1: Umkm}
     */
    private function publicProduct(array $productAttributes = [], array $productOverrides = [], array $umkmOverrides = []): array
    {
        $category = Category::query()->firstOrCreate(
            ['slug' => 'kuliner'],
            [
                'name' => 'Kuliner',
                'description' => 'Makanan dan minuman lokal.',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );

        $slug = $productAttributes['slug'] ?? 'nasi-detail';
        $umkm = Umkm::query()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Dapur Detail',
            'slug' => 'dapur-detail-'.$slug,
            'description' => 'Usaha kuliner lokal Cimuning.',
            'owner_name' => 'Ibu Detail',
            'whatsapp' => '081234567890',
            'address' => 'Jl. Cimuning Detail',
            'rw' => 'RW 04',
            'status' => 'verified',
            'is_active' => true,
        ], $umkmOverrides));

        $product = Product::query()->create(array_merge([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Detail',
            'slug' => $slug,
            'description' => 'Nasi box rumahan dengan lauk lengkap.',
            'price' => 25000,
            'image' => 'products/detail/nasi-detail.jpg',
            'is_active' => true,
            'is_admin_blocked' => false,
        ], $productAttributes, $productOverrides));

        return [$product, $umkm];
    }
}
