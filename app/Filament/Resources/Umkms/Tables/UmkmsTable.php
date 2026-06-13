<?php

namespace App\Filament\Resources\Umkms\Tables;

use App\Models\Umkm;
use App\Support\UmkmVerificationWorkflow;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
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
                    ->disk('public')
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
                TextColumn::make('view_count')
                    ->label('Dilihat')
                    ->numeric()
                    ->sortable(),
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
                TernaryFilter::make('service_delivery')
                    ->label('Delivery'),
                TernaryFilter::make('service_cod')
                    ->label('COD'),
            ])
            ->recordActions([
                Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin() && $record->status !== 'verified')
                    ->action(fn (Umkm $record) => UmkmVerificationWorkflow::verify($record)),
                Action::make('requestRevision')
                    ->label('Minta revisi')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin() && $record->status !== 'need_revision')
                    ->action(fn (Umkm $record) => UmkmVerificationWorkflow::requestRevision($record)),
                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Umkm $record): bool => Filament::auth()->user()?->isAdmin() && $record->status !== 'rejected')
                    ->action(fn (Umkm $record) => UmkmVerificationWorkflow::reject($record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Filament::auth()->user()?->isAdmin()),
                ]),
            ]);
    }
}
