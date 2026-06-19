<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UmkmSubmissionWorkflow;
use App\Support\UniqueSlug;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUmkm extends CreateRecord
{
    protected static string $resource = UmkmResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();
        $data = $this->prepareOwnerFriendlyData($data);

        if ($user?->isUmkmOwner()) {
            $data['user_id'] = $user->id;
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'umkm'), Umkm::class);
            $data['status'] = 'pending';
            $data['is_active'] = false;
            $data['is_featured'] = false;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if (Filament::auth()->user()?->isUmkmOwner()) {
            UmkmSubmissionWorkflow::submit(
                $this->record,
                Filament::auth()->user(),
                UmkmSubmissionWorkflow::payloadFromUmkm($this->record),
            );

            Notification::make()
                ->title('Profil usaha berhasil dikirim')
                ->body('Kami akan memberi tahu Anda setelah profil selesai diperiksa.')
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }

    private function prepareOwnerFriendlyData(array $data): array
    {
        if (blank($data['slug'] ?? null)) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'umkm'), Umkm::class);
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
