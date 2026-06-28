<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Support\AdminActivityLogger;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    public function getTitle(): string|Htmlable
    {
        return 'Profil & Keamanan Akun';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Perbarui identitas akun atau ganti password secara aman menggunakan password Anda saat ini.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Data Akun')
                ->description('Pastikan nama dan email yang digunakan masih benar dan dapat diakses.')
                ->schema([
                    $this->getNameFormComponent(),
                    $this->getEmailFormComponent(),
                ]),
            Section::make('Keamanan Akun')
                ->description('Pemulihan lewat email belum tersedia. Selama masih login, Anda dapat mengganti password dengan memasukkan password saat ini.')
                ->schema([
                    $this->getCurrentPasswordFormComponent(),
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                ]),
        ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return parent::getPasswordFormComponent()
            ->label('Password baru')
            ->belowContent('Gunakan password unik yang tidak dipakai pada akun lain.');
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return parent::getPasswordConfirmationFormComponent()
            ->label('Ulangi password baru');
    }

    protected function getCurrentPasswordFormComponent(): Component
    {
        return parent::getCurrentPasswordFormComponent()
            ->label('Password saat ini')
            ->belowContent('Wajib diisi saat mengganti email atau password.');
    }

    protected function handleRecordUpdate(Model $record, #[SensitiveParameter] array $data): Model
    {
        $before = Arr::only($record->attributesToArray(), ['name', 'email']);
        $authenticationChanged = filled($data['password'] ?? null);
        $updated = parent::handleRecordUpdate($record, $data);

        if (! $updated instanceof User || ! $updated->isAdmin()) {
            return $updated;
        }

        $after = Arr::only($updated->fresh()->attributesToArray(), ['name', 'email']);

        if ($before === $after && ! $authenticationChanged) {
            return $updated;
        }

        AdminActivityLogger::record(
            event: 'admin_profile_updated',
            actor: $updated,
            subject: $updated,
            reason: 'Admin memperbarui profil akunnya sendiri.',
            before: $before,
            after: $after,
            metadata: [
                'changed_fields' => collect(['name', 'email'])
                    ->filter(fn (string $field): bool => $before[$field] !== $after[$field])
                    ->values()
                    ->all(),
                'authentication_changed' => $authenticationChanged,
            ],
        );

        return $updated;
    }
}
