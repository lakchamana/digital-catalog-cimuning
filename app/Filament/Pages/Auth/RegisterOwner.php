<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class RegisterOwner extends Register
{
    public function mount(): void
    {
        $this->refreshCaptcha();

        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Akun Owner UMKM';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Buat akun owner UMKM';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString('Setelah membuat akun, lengkapi profil UMKM Anda untuk ditinjau admin sebelum tampil di direktori publik. Sudah punya akun? '.$this->loginAction->toHtml());
    }

    protected function mutateFormDataBeforeRegister(#[SensitiveParameter] array $data): array
    {
        if (filled($data['company_website'] ?? null)) {
            $this->refreshCaptcha();

            throw ValidationException::withMessages([
                'data.captcha_answer' => 'Verifikasi keamanan gagal. Silakan coba lagi.',
            ]);
        }

        if ((int) ($data['captcha_answer'] ?? -1) !== (int) Session::get('owner_register_captcha_answer')) {
            $this->refreshCaptcha();

            throw ValidationException::withMessages([
                'data.captcha_answer' => 'Jawaban verifikasi belum tepat.',
            ]);
        }

        $data['role'] = 'umkm_owner';
        unset($data['captcha_answer'], $data['company_website']);

        $this->refreshCaptcha();

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return parent::form($schema)
            ->components([
                $this->getNameFormComponent()
                    ->label('Nama lengkap'),
                $this->getEmailFormComponent()
                    ->label('Email aktif'),
                $this->getPasswordFormComponent()
                    ->label('Password'),
                $this->getPasswordConfirmationFormComponent()
                    ->label('Ulangi password'),
                $this->getCaptchaFormComponent(),
                TextInput::make('company_website')
                    ->label('Website perusahaan')
                    ->extraAttributes(['class' => 'hidden'])
                    ->autocomplete('off'),
            ]);
    }

    protected function getCaptchaFormComponent(): Component
    {
        return TextInput::make('captcha_answer')
            ->label('Verifikasi keamanan: '.Session::get('owner_register_captcha_question'))
            ->numeric()
            ->required()
            ->helperText('Jawab pertanyaan sederhana ini untuk memastikan pendaftaran bukan spam.');
    }

    private function refreshCaptcha(): void
    {
        $left = random_int(2, 9);
        $right = random_int(1, 9);

        Session::put('owner_register_captcha_question', "{$left} + {$right} = ?");
        Session::put('owner_register_captcha_answer', $left + $right);
    }
}
