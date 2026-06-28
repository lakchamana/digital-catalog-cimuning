<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class OwnerQuickActions extends Widget
{
    protected string $view = 'filament.widgets.owner-quick-actions';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isUmkmOwner() ?? false;
    }

    protected function getViewData(): array
    {
        $user = Filament::auth()->user();
        $umkm = Umkm::query()->where('user_id', $user?->id)->first();
        $hasProducts = $umkm?->products()->exists() ?? false;

        return [
            'profileUrl' => $umkm
                ? UmkmResource::getUrl('edit', ['record' => $umkm])
                : UmkmResource::getUrl('create'),
            'profileLabel' => $umkm ? 'Kelola Profil UMKM' : 'Lengkapi Profil UMKM',
            'productsUrl' => $umkm
                ? ($hasProducts ? ProductResource::getUrl('index') : ProductResource::getUrl('create'))
                : null,
            'productsLabel' => $hasProducts ? 'Kelola Produk/Jasa' : 'Tambah Produk/Jasa',
            'publicProfileUrl' => $umkm?->is_active && $umkm->status === 'verified' && ! $umkm->is_admin_blocked
                ? route('umkm.show', $umkm->slug)
                : null,
            'securityUrl' => route('filament.admin.auth.profile'),
        ];
    }
}
