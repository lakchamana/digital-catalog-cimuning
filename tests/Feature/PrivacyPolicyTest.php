<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RegisterOwner;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrivacyPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_renders_public_policy_sections(): void
    {
        $this->get(route('privacy'))
            ->assertOk()
            ->assertSee('Kebijakan Privasi Cimuning Digital Hub')
            ->assertSee('Data yang dikelola')
            ->assertSee('Pengunjung publik')
            ->assertSee('Owner UMKM')
            ->assertSee('Keamanan panel pengelola')
            ->assertSee('tidak menyimpan password, token, secret, IP mentah')
            ->assertSee('Pihak ketiga')
            ->assertSee('Hak pengguna')
            ->assertSee('Kontak dan pembaruan kebijakan')
            ->assertSee('UU No. 27 Tahun 2022')
            ->assertSee('cimuningppk@gmail.com')
            ->assertSee('0878-0405-4071')
            ->assertDontSee('cimuning_privacy_notice_seen_v1');
    }

    public function test_terms_page_renders_owner_catalog_rules_and_official_contact(): void
    {
        $this->get(route('terms'))
            ->assertOk()
            ->assertSee('Syarat Penggunaan Cimuning Digital Hub')
            ->assertSee('Kewajiban owner')
            ->assertSee('Konten, foto, dan hak penggunaan')
            ->assertSee('Konten dan penggunaan yang dilarang')
            ->assertSee('Komunikasi dan transaksi langsung')
            ->assertSee('Keamanan akun')
            ->assertSee('Penangguhan, penonaktifan, dan penghapusan')
            ->assertSee('hukum Republik Indonesia')
            ->assertSee('cimuningppk@gmail.com')
            ->assertSee('0878-0405-4071');
    }

    public function test_public_pages_link_to_privacy_policy_and_render_privacy_notice(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('privacy'), false)
            ->assertSee('Kebijakan Privasi')
            ->assertSee('data-privacy-notice="cimuning_privacy_notice_seen_v1"', false)
            ->assertSee('Mengerti');

        $supportUrl = 'https://wa.me/6287804054071?text='.
            rawurlencode('Halo Admin Cimuning Digital Hub, saya membutuhkan bantuan.');

        $this->get(route('home'))
            ->assertSee('data-support-whatsapp', false)
            ->assertSee('cimuning_support_hidden_session_v1')
            ->assertSee('aria-label="Tutup tombol bantuan WhatsApp"', false)
            ->assertSee('href="'.$supportUrl.'"', false)
            ->assertSee('target="_blank"', false)
            ->assertSee('rel="noopener noreferrer"', false)
            ->assertSee('bottom-5 md:bottom-6', false);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Kebijakan privasi')
            ->assertSee('Baca Kebijakan')
            ->assertSee(route('privacy'), false)
            ->assertSee(route('terms'), false)
            ->assertSee('cimuningppk@gmail.com')
            ->assertSee('0878-0405-4071');
    }

    public function test_owner_registration_requires_and_stores_privacy_acceptance(): void
    {
        $missingConsent = Livewire::test(RegisterOwner::class);
        $missingToken = session('owner_register_captcha_token');
        $missingAnswer = session("owner_register_captchas.{$missingToken}.answer");

        $missingConsent
            ->fillForm([
                'name' => 'Owner Tanpa Privasi',
                'email' => 'owner-tanpa-privasi@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'terms_accepted' => true,
                'captcha_token' => $missingToken,
                'captcha_answer' => $missingAnswer,
            ])
            ->call('register')
            ->assertHasFormErrors(['privacy_accepted']);

        $component = Livewire::test(RegisterOwner::class);
        $captchaToken = session('owner_register_captcha_token');
        $captchaAnswer = session("owner_register_captchas.{$captchaToken}.answer");

        $component
            ->fillForm([
                'name' => 'Owner Setuju Privasi',
                'email' => 'owner-setuju-privasi@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'privacy_accepted' => true,
                'terms_accepted' => true,
                'captcha_token' => $captchaToken,
                'captcha_answer' => $captchaAnswer,
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect(UmkmResource::getUrl('create'));

        $owner = User::query()->where('email', 'owner-setuju-privasi@example.test')->firstOrFail();

        $this->assertNotNull($owner->privacy_accepted_at);
        $this->assertSame(RegisterOwner::PRIVACY_VERSION, $owner->privacy_version);
        $this->assertNotNull($owner->terms_accepted_at);
        $this->assertSame(RegisterOwner::TERMS_VERSION, $owner->terms_version);
    }

    public function test_owner_registration_requires_terms_acceptance_separately(): void
    {
        $component = Livewire::test(RegisterOwner::class);
        $captchaToken = session('owner_register_captcha_token');
        $captchaAnswer = session("owner_register_captchas.{$captchaToken}.answer");

        $component
            ->fillForm([
                'name' => 'Owner Tanpa Syarat',
                'email' => 'owner-tanpa-syarat@example.test',
                'password' => 'password',
                'passwordConfirmation' => 'password',
                'privacy_accepted' => true,
                'captcha_token' => $captchaToken,
                'captcha_answer' => $captchaAnswer,
            ])
            ->call('register')
            ->assertHasFormErrors(['terms_accepted']);

        $this->assertDatabaseMissing('users', ['email' => 'owner-tanpa-syarat@example.test']);
    }

    public function test_sitemap_lists_privacy_policy_and_terms(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('privacy'), false)
            ->assertSee(route('terms'), false);
    }
}
