<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class OwnerUmkmStatus extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isUmkmOwner() ?? false;
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();

        return $table
            ->heading('Status UMKM saya')
            ->description('Lengkapi profil UMKM, tunggu verifikasi admin, lalu tambahkan produk atau jasa agar warga mudah menemukan usaha Anda.')
            ->query(
                Umkm::query()
                    ->with('category')
                    ->where('user_id', $user?->id)
                    ->latest(),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        'need_revision' => 'Perlu revisi',
                        default => 'Menunggu',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'verified' => 'success',
                        'rejected' => 'danger',
                        'need_revision' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('owner_guidance')
                    ->label('Arahan')
                    ->state(fn (Umkm $record): string => match ($record->status) {
                        'verified' => 'Profil sudah tampil. Tambahkan produk/jasa agar katalog lebih lengkap.',
                        'need_revision' => 'Perbaiki data profil sesuai arahan admin, lalu simpan ulang.',
                        'rejected' => 'Data belum bisa ditampilkan. Periksa kembali profil dan kontak usaha.',
                        default => 'Menunggu admin meninjau data usaha Anda.',
                    })
                    ->wrap(),
                IconColumn::make('is_active')
                    ->label('Tampil publik')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label('Update')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Umkm $record): string => UmkmResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Belum ada UMKM yang terhubung')
            ->emptyStateDescription('Daftarkan profil usaha agar dapat diperiksa dan ditampilkan di direktori UMKM Cimuning.')
            ->emptyStateActions([
                Action::make('createUmkm')
                    ->label('Lengkapi Profil UMKM')
                    ->url(UmkmResource::getUrl('create'))
                    ->icon('heroicon-o-plus-circle'),
                Action::make('createProduct')
                    ->label('Tambah Produk/Jasa')
                    ->url(ProductResource::getUrl('create'))
                    ->icon('heroicon-o-shopping-bag'),
            ]);
    }
}
