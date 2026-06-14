<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInformationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_complete_public_information(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('about').'">', false)
            ->assertSee('Tentang Cimuning Digital Hub')
            ->assertSee('Direktori UMKM Cimuning')
            ->assertSee('QR profil')
            ->assertSee('Google Maps')
            ->assertSee('Bukan marketplace transaksi')
            ->assertSee('Cari Produk/Jasa')
            ->assertDontSee('Tahap MVP');
    }

    public function test_contact_page_renders_help_ctas_without_fake_contact_details(): void
    {
        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="'.route('contact').'">', false)
            ->assertSee('Kontak & Bantuan')
            ->assertSee('Daftarkan UMKM')
            ->assertSee('Masuk Owner')
            ->assertSee('Cari Produk/Jasa')
            ->assertSee('Lihat Direktori UMKM')
            ->assertSee('Kontak pengelola akan diumumkan oleh tim Cimuning Digital Hub')
            ->assertDontSee('Informasi kontak pengelola akan ditambahkan')
            ->assertDontSee('Tahap MVP');
    }

    public function test_footer_links_to_about_and_contact_pages(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="'.route('about').'"', false)
            ->assertSee('Tentang Kami')
            ->assertSee('href="'.route('contact').'"', false)
            ->assertSee('Kontak/Bantuan');
    }
}
