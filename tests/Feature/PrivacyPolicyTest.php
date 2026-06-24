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
            ->assertSee('Pihak ketiga')
            ->assertSee('Hak pengguna')
            ->assertSee('Kontak dan pembaruan kebijakan')
            ->assertSee('UU No. 27 Tahun 2022')
            ->assertDontSee('cimuning_privacy_notice_seen_v1');
    }

    public function test_public_pages_link_to_privacy_policy_and_render_privacy_notice(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('privacy'), false)
            ->assertSee('Kebijakan Privasi')
            ->assertSee('data-privacy-notice="cimuning_privacy_notice_seen_v1"', false)
            ->assertSee('Mengerti');

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Kebijakan privasi')
            ->assertSee('Baca Kebijakan')
            ->assertSee(route('privacy'), false);
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
                'captcha_token' => $captchaToken,
                'captcha_answer' => $captchaAnswer,
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect(UmkmResource::getUrl('create'));

        $owner = User::query()->where('email', 'owner-setuju-privasi@example.test')->firstOrFail();

        $this->assertNotNull($owner->privacy_accepted_at);
        $this->assertSame(RegisterOwner::PRIVACY_VERSION, $owner->privacy_version);
    }

    public function test_sitemap_lists_privacy_policy(): void
    {
        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('privacy'), false);
    }
}
