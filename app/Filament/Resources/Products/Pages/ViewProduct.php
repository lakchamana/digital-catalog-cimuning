<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Support\ContentModeration;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('block')
                ->label('Blokir produk')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->schema([
                    Textarea::make('reason')
                        ->label('Alasan pemblokiran')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin() && ! $this->record->is_admin_blocked)
                ->action(function (array $data): void {
                    ContentModeration::blockProduct($this->record, Filament::auth()->user(), $data['reason']);
                    $this->record->refresh();
                    $this->successNotification('Produk diblokir');
                }),
            Action::make('rejectReview')
                ->label('Tolak peninjauan')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    Textarea::make('reason')
                        ->label('Alasan masih diblokir')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin()
                    && $this->record->is_admin_blocked
                    && filled($this->record->moderation_review_requested_at))
                ->action(function (array $data): void {
                    ContentModeration::rejectProductReview($this->record, Filament::auth()->user(), $data['reason']);
                    $this->record->refresh();
                    $this->successNotification('Permintaan review ditolak');
                }),
            Action::make('unblock')
                ->label(fn (): string => $this->record->moderation_review_requested_at
                    ? 'Setujui & aktifkan kembali'
                    : 'Aktifkan kembali')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->schema([
                    Textarea::make('reason')
                        ->label('Catatan keputusan')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isAdmin() && $this->record->is_admin_blocked)
                ->action(function (array $data): void {
                    ContentModeration::unblockProduct($this->record, Filament::auth()->user(), $data['reason']);
                    $this->record->refresh();
                    $this->successNotification('Produk dapat ditampilkan kembali');
                }),
        ];
    }

    private function successNotification(string $title): void
    {
        Notification::make()->title($title)->success()->send();
    }
}
