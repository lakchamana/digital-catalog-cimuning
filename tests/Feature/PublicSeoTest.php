<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_umkm_detail_renders_dynamic_seo_and_local_business_schema(): void
    {
        $umkm = $this->seedVerifiedUmkm([
            'cover_image' => 'umkm/covers/dapur-seo.jpg',
            'logo_image' => 'umkm/logos/dapur-seo.png',
            'latitude' => -6.3123456,
            'longitude' => 107.0123456,
        ]);

        $this->get(route('umkm.show', $umkm->slug))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('umkm.show', $umkm->slug).'">', false)
            ->assertSee('<meta name="description" content="Dapur SEO adalah UMKM Kuliner di RW 02, Jl. Cimuning SEO, Kota Bekasi.', false)
            ->assertSee('<meta property="og:type" content="business.business">', false)
            ->assertSee('<meta property="og:url" content="'.route('umkm.show', $umkm->slug).'">', false)
            ->assertSee('<meta property="og:image" content="'.asset('storage/umkm/covers/dapur-seo.jpg').'">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false)
            ->assertSee('<script type="application/ld+json">', false)
            ->assertSee('"@type": "LocalBusiness"', false)
            ->assertSee('"name": "Dapur SEO"', false)
            ->assertSee('"latitude": -6.3123456', false)
            ->assertSee('"makesOffer"', false);
    }

    public function test_umkm_detail_social_image_falls_back_to_logo_then_brand(): void
    {
        $logoUmkm = $this->seedVerifiedUmkm([
            'slug' => 'logo-seo',
            'name' => 'Logo SEO',
            'cover_image' => null,
            'logo_image' => 'umkm/logos/logo-seo.png',
        ]);
        $brandUmkm = $this->seedVerifiedUmkm([
            'slug' => 'brand-seo',
            'name' => 'Brand SEO',
            'cover_image' => null,
            'logo_image' => null,
        ]);

        $this->get(route('umkm.show', $logoUmkm->slug))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.asset('storage/umkm/logos/logo-seo.png').'">', false);

        $this->get(route('umkm.show', $brandUmkm->slug))
            ->assertOk()
            ->assertSee('<meta property="og:image" content="'.asset('assets/brand/logo-cimuning.png').'">', false);
    }

    public function test_sitemap_lists_only_public_indexable_urls(): void
    {
        $category = $this->category();
        $verified = $this->seedVerifiedUmkm(['slug' => 'sitemap-verified', 'name' => 'Sitemap Verified']);
        $this->seedVerifiedUmkm(['slug' => 'sitemap-inactive', 'name' => 'Sitemap Inactive', 'is_active' => false]);
        $this->seedVerifiedUmkm(['slug' => 'sitemap-pending', 'name' => 'Sitemap Pending', 'status' => 'pending', 'is_active' => false]);
        $publicProduct = $verified->products()->firstOrFail();
        Product::query()->create([
            'umkm_id' => $verified->id,
            'category_id' => $category->id,
            'name' => 'Produk Sitemap Blocked',
            'slug' => 'produk-sitemap-blocked',
            'is_active' => true,
            'is_admin_blocked' => true,
        ]);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false)
            ->assertSee(route('home'), false)
            ->assertSee(route('products.index'), false)
            ->assertSee(route('umkm.index'), false)
            ->assertSee(route('categories.index'), false)
            ->assertSee(route('categories.show', $category->slug), false)
            ->assertSee(route('umkm.show', $verified->slug), false)
            ->assertSee(route('products.show', $publicProduct->slug), false)
            ->assertDontSee('sitemap-inactive')
            ->assertDontSee('sitemap-pending')
            ->assertDontSee('produk-sitemap-blocked')
            ->assertDontSee('/admin')
            ->assertDontSee('/leads/');
    }

    public function test_robots_file_references_sitemap_and_disallows_admin(): void
    {
        $this->get(route('robots'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSeeText('Disallow: /admin')
            ->assertSeeText('Sitemap: '.route('sitemap'));
    }

    private function seedVerifiedUmkm(array $attributes = []): Umkm
    {
        $category = $this->category();
        $slug = $attributes['slug'] ?? 'dapur-seo';

        $umkm = Umkm::query()->create(array_merge([
            'category_id' => $category->id,
            'name' => 'Dapur SEO',
            'slug' => $slug,
            'description' => 'Nasi box, kue basah, dan katering rumahan untuk warga Cimuning.',
            'owner_name' => 'Ibu SEO',
            'whatsapp' => '081234567890',
            'email' => 'seo@example.test',
            'address' => 'Jl. Cimuning SEO, Kota Bekasi',
            'rw' => 'RW 02',
            'status' => 'verified',
            'is_active' => true,
        ], $attributes));

        Product::query()->create([
            'umkm_id' => $umkm->id,
            'category_id' => $category->id,
            'name' => 'Nasi Box SEO',
            'slug' => $slug.'-nasi-box',
            'description' => 'Paket nasi box rumahan.',
            'price' => 25000,
            'is_active' => true,
        ]);

        return $umkm->refresh()->load(['category', 'products']);
    }

    private function category(): Category
    {
        return Category::query()->firstOrCreate(
            ['slug' => 'kuliner'],
            [
                'name' => 'Kuliner',
                'description' => 'Makanan dan minuman lokal.',
                'is_active' => true,
                'sort_order' => 1,
            ],
        );
    }
}
