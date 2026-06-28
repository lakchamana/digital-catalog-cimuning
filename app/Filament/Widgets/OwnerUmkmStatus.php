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
                    ->with(['category', 'latestSubmission'])
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
                    ->state(fn (Umkm $record): string => $record->latestSubmission?->status ?? $record->status)
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'approved', 'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        'need_revision' => 'Perlu revisi',
                        'superseded' => 'Diperbarui',
                        default => 'Menunggu review',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'approved', 'verified' => 'success',
                        'rejected' => 'danger',
                        'need_revision' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('owner_guidance')
                    ->label('Arahan')
                    ->state(function (Umkm $record): string {
                        $submission = $record->latestSubmission;

                        if ($record->is_admin_blocked) {
                            return 'Profil dinonaktifkan pengelola. Baca catatan admin sebelum menghubungi pengelola.';
                        }

                        return match ($submission?->status ?? $record->status) {
                            'approved', 'verified' => 'Profil sudah tampil di direktori publik.',
                            'need_revision' => 'Perbaiki profil sesuai catatan admin, lalu kirim kembali.',
                            'rejected' => 'Pengajuan ditolak, tetapi Anda dapat memperbaiki dan mengajukan kembali.',
                            default => $submission?->type === 'update'
                                ? 'Perubahan sedang ditinjau. Profil lama tetap tayang.'
                                : 'Menunggu admin meninjau data usaha Anda.',
                        };
                    })
                    ->wrap(),
                TextColumn::make('latestSubmission.review_notes')
                    ->label('Catatan admin')
                    ->placeholder('-')
                    ->wrap(),
                TextColumn::make('admin_block_reason')
                    ->label('Catatan penonaktifan')
                    ->visible(fn (): bool => Umkm::query()->where('user_id', $user?->id)->where('is_admin_blocked', true)->exists())
                    ->placeholder('-')
                    ->wrap(),
                IconColumn::make('public_visibility')
                    ->label('Tampil publik')
                    ->state(fn (Umkm $record): bool => $record->is_active && $record->status === 'verified' && ! $record->is_admin_blocked)
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
