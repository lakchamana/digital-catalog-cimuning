<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use App\Support\AdminActivityLogger;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
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
