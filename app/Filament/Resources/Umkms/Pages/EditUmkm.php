<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UniqueSlug;
use Filament\Actions\Action;
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
            Action::make('downloadQr')
                ->label('Download QR')
                ->icon('heroicon-o-qr-code')
                ->color('gray')
                ->url(fn (): string => route('qr.umkm.svg', [
                    'umkm' => $this->record->slug,
                    'download' => 1,
                ]))
                ->visible(fn (): bool => $this->record->is_active && $this->record->status === 'verified')
                ->openUrlInNewTab(),
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
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if (! Filament::auth()->user()?->isUmkmOwner()) {
            return;
        }

        Notification::make()
            ->title('Perubahan berhasil disimpan')
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
