<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Product;
use App\Models\Umkm;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class OwnerOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isUmkmOwner() ?? false;
    }

    protected function getStats(): array
    {
        $user = Filament::auth()->user();
        $umkm = Umkm::query()
            ->with('latestSubmission')
            ->where('user_id', $user?->id)
            ->first();

        $products = Product::query()
            ->whereHas('umkm', fn (Builder $query): Builder => $query->where('user_id', $user?->id));

        $totalProducts = (clone $products)->count();
        $publicProducts = (clone $products)->publiclyVisible()->count();
        $blockedProductsNeedingAction = (clone $products)
            ->where('is_admin_blocked', true)
            ->whereNull('moderation_review_requested_at')
            ->count();

        $profileNeedsAction = ! $umkm
            || $umkm->is_admin_blocked
            || in_array($umkm->latestSubmission?->status ?? $umkm->status, ['need_revision', 'rejected'], true);

        $actionCount = ($profileNeedsAction ? 1 : 0) + $blockedProductsNeedingAction;
        $profileUrl = $umkm
            ? UmkmResource::getUrl('edit', ['record' => $umkm])
            : UmkmResource::getUrl('create');
        $productsUrl = $umkm ? ProductResource::getUrl('index') : $profileUrl;

        [$profileStatus, $profileDescription, $profileColor, $profileIcon] = $this->profilePresentation($umkm);

        return [
            Stat::make('Status Profil UMKM', $profileStatus)
                ->description($profileDescription)
                ->color($profileColor)
                ->icon($profileIcon)
                ->url($profileUrl),
            Stat::make('Produk/Jasa', $totalProducts)
                ->description($totalProducts > 0 ? 'Kelola katalog usaha Anda' : 'Tambahkan produk atau jasa pertama')
                ->color('info')
                ->icon('heroicon-o-shopping-bag')
                ->url($productsUrl),
            Stat::make('Tampil Publik', $publicProducts)
                ->description('Produk yang dapat ditemukan masyarakat')
                ->color($publicProducts > 0 ? 'success' : 'gray')
                ->icon('heroicon-o-eye')
                ->url($productsUrl),
            Stat::make('Perlu Tindakan', $actionCount)
                ->description($actionCount > 0 ? 'Buka dan selesaikan catatan yang tersedia' : 'Tidak ada perbaikan yang tertunda')
                ->color($actionCount > 0 ? 'warning' : 'success')
                ->icon($actionCount > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->url($profileNeedsAction ? $profileUrl : $productsUrl),
        ];
    }

    /**
     * @return array{string, string, string, string}
     */
    private function profilePresentation(?Umkm $umkm): array
    {
        if (! $umkm) {
            return ['Belum lengkap', 'Lengkapi profil usaha untuk mulai ditinjau', 'warning', 'heroicon-o-pencil-square'];
        }

        if ($umkm->is_admin_blocked) {
            return ['Dinonaktifkan', 'Baca alasan dari admin dan perbaiki data', 'danger', 'heroicon-o-no-symbol'];
        }

        $submissionStatus = $umkm->latestSubmission?->status;

        if ($submissionStatus === 'need_revision') {
            return ['Perlu revisi', 'Perbaiki profil sesuai catatan admin', 'warning', 'heroicon-o-pencil-square'];
        }

        if ($submissionStatus === 'rejected') {
            return ['Ditolak', 'Perbaiki data sebelum mengajukan kembali', 'danger', 'heroicon-o-x-circle'];
        }

        if ($submissionStatus === 'pending') {
            return $umkm->status === 'verified'
                ? ['Perubahan ditinjau', 'Profil lama tetap tampil selama pemeriksaan', 'info', 'heroicon-o-clock']
                : ['Menunggu review', 'Admin sedang memeriksa profil usaha', 'warning', 'heroicon-o-clock'];
        }

        if ($umkm->is_active && $umkm->status === 'verified') {
            return ['Terverifikasi', 'Profil sudah tampil di direktori publik', 'success', 'heroicon-o-check-badge'];
        }

        return ['Belum ditayangkan', 'Lengkapi profil dan tunggu persetujuan admin', 'gray', 'heroicon-o-clock'];
    }
}
