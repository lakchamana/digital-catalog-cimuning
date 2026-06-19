<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\UniqueSlug;
use Filament\Actions\DeleteAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
