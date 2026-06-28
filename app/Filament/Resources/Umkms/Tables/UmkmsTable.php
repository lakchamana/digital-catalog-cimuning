<?php

namespace App\Filament\Resources\Umkms\Tables;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use App\Support\ContentModeration;
use App\Support\UploadDisk;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UmkmsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_image')
                    ->label('Logo')
                    ->disk(UploadDisk::name())
                    ->circular(),
                TextColumn::make('owner.name')
                    ->label('Akun')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('owner_name')
                    ->label('Penanggung jawab')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('rw')
                    ->label('RW')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Verifikasi')
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
                IconColumn::make('is_featured')
                    ->label('Pilihan')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('is_admin_blocked')
                    ->label('Diblokir admin')
                    ->boolean()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'gray'),
                IconColumn::make('service_delivery')
                    ->label('Delivery')
                    ->boolean(),
                IconColumn::make('service_cod')
                    ->label('COD')
                    ->boolean(),
                IconColumn::make('service_custom_order')
                    ->label('Custom')
                    ->boolean(),
                IconColumn::make('has_physical_store')
                    ->label('Toko')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status verifikasi')
                    ->options([
                        'pending' => 'Menunggu',
                        'verified' => 'Terverifikasi',
                        'rejected' => 'Ditolak',
                        'need_revision' => 'Perlu revisi',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('rw')
                    ->label('RW')
                    ->options(fn () => Umkm::query()
                        ->whereNotNull('rw')
                        ->distinct()
                        ->orderBy('rw')
                        ->pluck('rw', 'rw')
                        ->all()),
                TernaryFilter::make('is_active')
                    ->label('Aktif'),
                TernaryFilter::make('is_admin_blocked')
                    ->label('Diblokir admin'),
                TernaryFilter::make('service_delivery')
                    ->label('Delivery'),
                TernaryFilter::make('service_cod')
                    ->label('COD'),
            ])
            ->recordUrl(fn (Umkm $record): string => Filament::auth()->user()?->isAdmin()
                ? UmkmResource::getUrl('view', ['record' => $record])
                : UmkmResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('blockPublication')
                    ->label('Nonaktifkan publikasi')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')->label('Alasan penonaktifan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin()
                        && $record->status === 'verified'
                        && $record->is_active
                        && ! $record->is_admin_blocked)
                    ->action(function (Umkm $record, array $data): void {
                        ContentModeration::blockUmkm($record, Filament::auth()->user(), $data['reason']);
                        Notification::make()
                            ->title('Publikasi UMKM dinonaktifkan')
                            ->body('Profil dan seluruh produknya disembunyikan dari halaman publik. Owner telah menerima notifikasi.')
                            ->success()
                            ->send();
                    }),
                Action::make('restorePublication')
                    ->label('Pulihkan publikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->schema([
                        Textarea::make('reason')->label('Catatan pemulihan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin()
                        && $record->is_admin_blocked
                        && ! in_array($record->owner?->account_status, ['anonymization_pending', 'anonymized'], true))
                    ->action(function (Umkm $record, array $data): void {
                        ContentModeration::unblockUmkm($record, Filament::auth()->user(), $data['reason']);
                        Notification::make()
                            ->title('Publikasi UMKM dipulihkan')
                            ->body('Profil dapat tampil kembali bila statusnya tetap aktif dan terverifikasi.')
                            ->success()
                            ->send();
                    }),
                Action::make('toggleFeatured')
                    ->label(fn (Umkm $record): string => $record->is_featured ? 'Hapus dari pilihan' : 'Jadikan pilihan')
                    ->icon('heroicon-o-star')
                    ->color('info')
                    ->requiresConfirmation()
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin()
                        && $record->status === 'verified'
                        && $record->is_active
                        && ! $record->is_admin_blocked)
                    ->action(fn (Umkm $record) => ContentModeration::setFeatured(
                        $record,
                        Filament::auth()->user(),
                        ! $record->is_featured,
                    )),
                Action::make('downloadQr')
                    ->label('Download QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn (Umkm $record): string => route('qr.umkm.svg', [
                        'umkm' => $record->slug,
                        'download' => 1,
                    ]))
                    ->visible(fn (Umkm $record): bool => $record->is_active && $record->status === 'verified')
                    ->openUrlInNewTab(),
                ViewAction::make()->visible(fn (): bool => Filament::auth()->user()?->isAdmin() ?? false),
                EditAction::make()->visible(fn (): bool => Filament::auth()->user()?->isUmkmOwner() ?? false),
            ]);
    }
}
