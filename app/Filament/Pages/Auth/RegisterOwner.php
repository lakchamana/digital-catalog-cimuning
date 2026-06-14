<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use SensitiveParameter;

class RegisterOwner extends Register
{
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
        $data['role'] = 'umkm_owner';

        return $data;
    }
}
