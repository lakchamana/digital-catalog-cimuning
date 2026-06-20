<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\ContentModeration;
use App\Support\UniqueSlug;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('requestReview')
                ->label('Ajukan peninjauan ulang')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->schema([
                    Textarea::make('note')
                        ->label('Perbaikan yang sudah dilakukan')
                        ->required()->minLength(10)->maxLength(2000),
                ])
                ->visible(fn (): bool => Filament::auth()->user()?->isUmkmOwner()
                    && $this->record->is_admin_blocked
                    && blank($this->record->moderation_review_requested_at))
                ->action(function (array $data): void {
                    ContentModeration::requestProductReview($this->record, Filament::auth()->user(), $data['note']);
                    $this->record->refresh();
                    Notification::make()
                        ->title('Permintaan peninjauan dikirim')
                        ->body('Produk tetap tidak tampil sampai admin menyetujui perbaikan.')
                        ->success()
                        ->send();
                }),
            DeleteAction::make()->visible(fn (): bool => Filament::auth()->user()?->isUmkmOwner() ?? false),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'produk'), Product::class, ignoreId: $this->record->getKey());

            $ownsUmkm = Umkm::query()
                ->whereKey($data['umkm_id'] ?? null)
                ->where('user_id', $user->id)
                ->exists();

            abort_unless($ownsUmkm, 403);
        }

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'produk'), Product::class, ignoreId: $this->record->getKey());
        }

        return $data;
    }
}
