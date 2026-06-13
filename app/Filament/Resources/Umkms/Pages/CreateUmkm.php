<?php

namespace App\Filament\Resources\Umkms\Pages;

use App\Filament\Resources\Umkms\UmkmResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;

class CreateUmkm extends CreateRecord
{
    protected static string $resource = UmkmResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            $data['user_id'] = $user->id;
            $data['status'] = 'pending';
            $data['is_featured'] = false;
            $data['view_count'] = 0;
        }

        return $data;
    }
}
