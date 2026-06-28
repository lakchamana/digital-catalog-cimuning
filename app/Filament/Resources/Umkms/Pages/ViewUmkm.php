<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Support\ContentModeration;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUmkm extends ViewRecord
{
    protected static string $resource = UmkmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('blockPublication')
                ->label('Nonaktifkan publikasi')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->schema([
                    Textarea::make('reason')->label('Alasan penonaktifan')->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin()
                    && $this->record->status === 'verified'
                    && $this->record->is_active
                    && ! $this->record->is_admin_blocked)
                ->action(function (array $data): void {
                    ContentModeration::blockUmkm($this->record, Filament::auth()->user(), $data['reason']);
                    $this->record->refresh();
                    Notification::make()->title('Publikasi UMKM dinonaktifkan')->success()->send();
                }),
            Action::make('restorePublication')
                ->label('Pulihkan publikasi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->schema([
                    Textarea::make('reason')->label('Catatan pemulihan')->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin()
                    && $this->record->is_admin_blocked
                    && ! in_array($this->record->owner?->account_status, ['anonymization_pending', 'anonymized'], true))
                ->action(function (array $data): void {
                    ContentModeration::unblockUmkm($this->record, Filament::auth()->user(), $data['reason']);
                    $this->record->refresh();
                    Notification::make()->title('Publikasi UMKM dipulihkan')->success()->send();
                }),
        ];
    }
}
