<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class PlatformStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $umkmQuery = Umkm::query();
        $productQuery = Product::query();

        if ($user?->isUmkmOwner()) {
            $umkmQuery->where('user_id', $user->id);
            $productQuery->whereHas('umkm', fn (Builder $query) => $query->where('user_id', $user->id));
        }

        return [
            Stat::make('UMKM terverifikasi', (clone $umkmQuery)->where('status', 'verified')->count())
                ->description('Tampil di direktori publik')
                ->color('success')
                ->icon('heroicon-o-check-badge'),
            Stat::make('Menunggu verifikasi', (clone $umkmQuery)->where('status', 'pending')->count())
                ->description('Perlu ditinjau admin')
                ->color('warning')
                ->icon('heroicon-o-clock'),
            Stat::make('Produk aktif', (clone $productQuery)->where('is_active', true)->count())
                ->description('Masuk katalog produk digital')
                ->color('info')
                ->icon('heroicon-o-shopping-bag'),
            Stat::make('Kategori aktif', Category::query()->where('is_active', true)->count())
                ->description('Dipakai untuk navigasi publik')
                ->color('gray')
                ->icon('heroicon-o-tag'),
        ];
    }
}
