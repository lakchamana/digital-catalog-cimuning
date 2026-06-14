<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;
use SensitiveParameter;

class RegisterOwner extends Register
{
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
        $token = (string) ($data['captcha_token'] ?? '');
        $captcha = $this->captchaForToken($token);

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
        unset($data['captcha_answer'], $data['captcha_token'], $data['profile_confirmation']);

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
            ->helperText('Jawab pertanyaan sederhana ini untuk memastikan pendaftaran bukan spam.');
    }

    protected function getHoneypotFormComponent(): Component
    {
        return TextInput::make('profile_confirmation')
            ->label('Konfirmasi')
            ->hiddenLabel()
            ->autocomplete('new-password')
            ->extraFieldWrapperAttributes(['class' => 'hidden'], merge: true)
            ->extraAttributes([
                'aria-hidden' => 'true',
                'tabindex' => '-1',
            ]);
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
