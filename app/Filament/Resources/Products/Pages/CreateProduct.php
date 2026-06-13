<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Umkm;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            $ownsUmkm = Umkm::query()
                ->whereKey($data['umkm_id'] ?? null)
                ->where('user_id', $user->id)
                ->exists();

            abort_unless($ownsUmkm, 403);
        }

        return $data;
    }
}
