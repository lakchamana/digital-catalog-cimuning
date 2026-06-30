<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class RegisterOwner extends Register
{
    public const PRIVACY_VERSION = '2026-06-29';

    public const TERMS_VERSION = '2026-06-29';

    private const CAPTCHA_SESSION_KEY = 'owner_register_captchas';

    private const CURRENT_CAPTCHA_TOKEN_KEY = 'owner_register_captcha_token';

    private const CURRENT_CAPTCHA_QUESTION_KEY = 'owner_register_captcha_question';

    public function mount(): void
    {
        $this->refreshCaptcha();

        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        return 'Daftar Akun UMKM';
    }

    public function getHeading(): string|Htmlable|null
    {
        return 'Buat akun UMKM';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return new HtmlString('Gunakan email aktif untuk mengelola profil dan produk usaha Anda. Sudah punya akun? '.$this->loginAction->toHtml());
    }

    protected function mutateFormDataBeforeRegister(#[SensitiveParameter] array $data): array
    {
        $token = (string) ($data['captcha_token'] ?? '');
        $captcha = $this->captchaForToken($token);

        if (! in_array($data['privacy_accepted'] ?? false, [true, 1, '1', 'on'], true)) {
            throw ValidationException::withMessages([
                'data.privacy_accepted' => 'Anda perlu menyetujui Kebijakan Privasi sebelum membuat akun UMKM.',
            ]);
        }

        if (! in_array($data['terms_accepted'] ?? false, [true, 1, '1', 'on'], true)) {
            throw ValidationException::withMessages([
                'data.terms_accepted' => 'Anda perlu menyetujui Syarat Penggunaan sebelum membuat akun UMKM.',
            ]);
        }

        if (filled($data['profile_confirmation'] ?? null)) {
            $this->refreshCaptchaState();

            throw ValidationException::withMessages([
                'data.captcha_answer' => 'Verifikasi keamanan gagal. Silakan muat ulang halaman bila pesan ini muncul kembali.',
            ]);
        }

        if (! $captcha || $this->normalizedCaptchaAnswer($data['captcha_answer'] ?? null) !== (int) $captcha['answer']) {
            $this->refreshCaptchaState();

            throw ValidationException::withMessages([
                'data.captcha_answer' => $captcha ? 'Jawaban verifikasi belum tepat.' : 'Sesi verifikasi berubah. Silakan jawab pertanyaan baru.',
            ]);
        }

        $data['role'] = 'umkm_owner';
        $data['privacy_accepted_at'] = now();
        $data['privacy_version'] = self::PRIVACY_VERSION;
        $data['terms_accepted_at'] = now();
        $data['terms_version'] = self::TERMS_VERSION;
        unset($data['captcha_answer'], $data['captcha_token'], $data['profile_confirmation'], $data['privacy_accepted'], $data['terms_accepted']);

        $this->forgetCaptcha($token);

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
                $this->getPrivacyAcceptanceFormComponent(),
                $this->getTermsAcceptanceFormComponent(),
                $this->getCaptchaTokenFormComponent(),
                $this->getCaptchaFormComponent(),
                $this->getHoneypotFormComponent(),
            ]);
    }

    protected function getCaptchaTokenFormComponent(): Component
    {
        return Hidden::make('captcha_token')
            ->default(fn () => Session::get(self::CURRENT_CAPTCHA_TOKEN_KEY));
    }

    protected function getCaptchaFormComponent(): Component
    {
        return TextInput::make('captcha_answer')
            ->label('Verifikasi keamanan: '.Session::get(self::CURRENT_CAPTCHA_QUESTION_KEY))
            ->inputMode('numeric')
            ->required()
            ->helperText('Masukkan hasil perhitungan di atas.');
    }

    protected function getHoneypotFormComponent(): Component
    {
        return Hidden::make('profile_confirmation')
            ->default('');
    }

    protected function getPrivacyAcceptanceFormComponent(): Component
    {
        return Checkbox::make('privacy_accepted')
            ->label(new HtmlString('Saya telah membaca dan menyetujui <a href="'.route('privacy').'" target="_blank" rel="noopener" class="font-semibold text-primary-600 underline">Kebijakan Privasi Cimuning Digital Hub</a>.'))
            ->accepted()
            ->required();
    }

    protected function getTermsAcceptanceFormComponent(): Component
    {
        return Checkbox::make('terms_accepted')
            ->label(new HtmlString('Saya telah membaca dan menyetujui <a href="'.route('terms').'" target="_blank" rel="noopener" class="font-semibold text-primary-600 underline">Syarat Penggunaan Cimuning Digital Hub</a>.'))
            ->accepted()
            ->required();
    }

    private function refreshCaptcha(): string
    {
        $left = random_int(2, 9);
        $right = random_int(1, 9);
        $token = (string) Str::uuid();
        $captchas = Session::get(self::CAPTCHA_SESSION_KEY, []);

        $captchas[$token] = [
            'question' => "{$left} + {$right} = ?",
            'answer' => $left + $right,
        ];

        if (count($captchas) > 8) {
            $captchas = array_slice($captchas, -8, null, true);
        }

        Session::put(self::CAPTCHA_SESSION_KEY, $captchas);
        Session::put(self::CURRENT_CAPTCHA_TOKEN_KEY, $token);
        Session::put(self::CURRENT_CAPTCHA_QUESTION_KEY, $captchas[$token]['question']);

        return $token;
    }

    private function refreshCaptchaState(): void
    {
        $this->data['captcha_token'] = $this->refreshCaptcha();
        $this->data['captcha_answer'] = null;
        $this->data['profile_confirmation'] = null;
    }

    /**
     * @return array{question: string, answer: int}|null
     */
    private function captchaForToken(string $token): ?array
    {
        $captcha = Session::get(self::CAPTCHA_SESSION_KEY, [])[$token] ?? null;

        return is_array($captcha) ? $captcha : null;
    }

    private function forgetCaptcha(string $token): void
    {
        $captchas = Session::get(self::CAPTCHA_SESSION_KEY, []);

        unset($captchas[$token]);

        Session::put(self::CAPTCHA_SESSION_KEY, $captchas);
    }

    private function normalizedCaptchaAnswer(mixed $answer): int
    {
        $answer = trim((string) $answer);

        return ctype_digit($answer) ? (int) $answer : -1;
    }
}
