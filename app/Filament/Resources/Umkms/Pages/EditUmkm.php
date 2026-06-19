<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UmkmSubmissionWorkflow;
use App\Support\UniqueSlug;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

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
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (Filament::auth()->user()?->isUmkmOwner()) {
            return UmkmSubmissionWorkflow::formData($this->record);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();
        $data = $this->prepareOwnerFriendlyData($data);

        if ($user?->isUmkmOwner()) {
            unset($data['user_id']);
            $data['slug'] = $this->record->slug;
            unset($data['status'], $data['is_active'], $data['is_featured']);
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            UmkmSubmissionWorkflow::submit($this->record, $user, $data);

            return $record;
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getSavedNotification(): ?Notification
    {
        if (! Filament::auth()->user()?->isUmkmOwner()) {
            return parent::getSavedNotification();
        }

        return Notification::make()
            ->title('Perubahan dikirim untuk ditinjau')
            ->body($this->record->status === 'verified'
                ? 'Profil yang sedang tayang tetap digunakan sampai perubahan disetujui admin.'
                : 'Kami akan memberi tahu Anda setelah pengajuan selesai diperiksa.')
            ->success();
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
