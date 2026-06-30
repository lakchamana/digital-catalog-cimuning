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

        $actions = [[
            'label' => $umkm ? 'Kelola Profil UMKM' : 'Lengkapi Profil UMKM',
            'description' => $umkm ? 'Perbarui informasi usaha Anda' : 'Isi data usaha untuk mulai ditinjau',
            'url' => $umkm
                ? UmkmResource::getUrl('edit', ['record' => $umkm])
                : UmkmResource::getUrl('create'),
            'icon' => 'heroicon-o-building-storefront',
            'primary' => true,
            'external' => false,
        ]];

        if ($umkm) {
            $actions[] = [
                'label' => $hasProducts ? 'Kelola Produk/Jasa' : 'Tambah Produk/Jasa',
                'description' => $hasProducts ? 'Atur katalog yang dimiliki usaha' : 'Tambahkan isi katalog pertama',
                'url' => $hasProducts ? ProductResource::getUrl('index') : ProductResource::getUrl('create'),
                'icon' => 'heroicon-o-shopping-bag',
                'primary' => false,
                'external' => false,
            ];
        }

        if ($umkm?->is_active && $umkm->status === 'verified' && ! $umkm->is_admin_blocked) {
            $actions[] = [
                'label' => 'Lihat Profil Publik',
                'description' => 'Periksa tampilan yang dilihat masyarakat',
                'url' => route('umkm.show', $umkm->slug),
                'icon' => 'heroicon-o-arrow-top-right-on-square',
                'primary' => false,
                'external' => true,
            ];
        }

        $actions[] = [
            'label' => 'Keamanan Akun',
            'description' => 'Ubah nama, email, atau password',
            'url' => route('filament.admin.auth.profile'),
            'icon' => 'heroicon-o-shield-check',
            'primary' => false,
            'external' => false,
        ];

        $supportPhone = preg_replace('/\D+/', '', (string) config('support.whatsapp'));
        $supportMessage = rawurlencode((string) config('support.whatsapp_message'));

        $actions[] = [
            'label' => 'Hubungi Bantuan',
            'description' => 'WhatsApp admin atau '.config('support.email'),
            'url' => "https://wa.me/{$supportPhone}?text={$supportMessage}",
            'icon' => 'heroicon-o-chat-bubble-left-right',
            'primary' => false,
            'external' => true,
        ];

        return [
            'actions' => $actions,
        ];
    }
}
