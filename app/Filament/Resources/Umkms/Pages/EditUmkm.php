<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UniqueSlug;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditUmkm extends EditRecord
{
    protected static string $resource = UmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();
        $data = $this->prepareOwnerFriendlyData($data);

        if ($user?->isUmkmOwner()) {
            unset($data['user_id']);
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'umkm'), Umkm::class, ignoreId: $this->record->getKey());
            $data['status'] = $this->record->status;
            $data['is_featured'] = $this->record->is_featured;
            $data['view_count'] = $this->record->view_count;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (! Filament::auth()->user()?->isUmkmOwner()) {
            return;
        }

        Notification::make()
            ->title('Perubahan profil UMKM tersimpan')
            ->body('Admin dapat meninjau data terbaru sebelum status publik berubah.')
            ->success()
            ->send();
    }

    private function prepareOwnerFriendlyData(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'umkm'), Umkm::class, ignoreId: $this->record->getKey());
        }

        if ($coordinates = OwnerFormHelper::coordinatesFromMapsText($data['maps_link'] ?? null)) {
            $data['latitude'] = $coordinates['latitude'];
            $data['longitude'] = $coordinates['longitude'];
        }

        $data['instagram'] = OwnerFormHelper::normalizeInstagram($data['instagram'] ?? null);
        $data['tiktok'] = OwnerFormHelper::normalizeTiktok($data['tiktok'] ?? null);
        unset($data['maps_link']);

        return $data;
    }
}
