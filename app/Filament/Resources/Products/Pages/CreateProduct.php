<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use App\Models\Umkm;
use App\Support\UniqueSlug;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'produk'), Product::class);

            $ownsUmkm = Umkm::query()
                ->whereKey($data['umkm_id'] ?? null)
                ->where('user_id', $user->id)
                ->exists();

            abort_unless($ownsUmkm, 403);
        }

        if (blank($data['slug'] ?? null)) {
            $data['slug'] = UniqueSlug::make((string) ($data['name'] ?? 'produk'), Product::class);
        }

        return $data;
    }
}
