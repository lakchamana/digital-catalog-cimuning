<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Umkm;
use App\Models\UmkmSubmission;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PlatformStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    protected function getStats(): array
    {
        $umkmQuery = Umkm::query();
        $productQuery = Product::query();
        $pendingSubmissions = UmkmSubmission::query()->where('status', 'pending');

        return [
            Stat::make('UMKM terverifikasi', (clone $umkmQuery)->publiclyVisible()->count())
                ->description('Tampil di direktori publik')
                ->color('success')
                ->icon('heroicon-o-check-badge'),
            Stat::make('Menunggu verifikasi', $pendingSubmissions->count())
                ->description('Perlu ditinjau admin')
                ->color('warning')
                ->icon('heroicon-o-clock'),
            Stat::make('Produk aktif', (clone $productQuery)->publiclyVisible()->count())
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
