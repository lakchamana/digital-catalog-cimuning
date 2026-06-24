<?php

namespace App\Filament\Resources\Products\Tables;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
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
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->state(fn (Product $record): ?string => $record->image ?: $record->images->first()?->path)
                    ->disk(UploadDisk::name())
                    ->square(),
                TextColumn::make('umkm.name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('images_count')
                    ->label('Galeri')
                    ->counts('images')
                    ->badge()
                    ->formatStateUsing(fn (int|string|null $state): string => ((int) $state).' foto')
                    ->color(fn (int|string|null $state): string => ((int) $state) > 0 ? 'info' : 'gray'),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                IconColumn::make('is_admin_blocked')
                    ->label('Diblokir admin')
                    ->boolean()
                    ->color(fn (bool $state): string => $state ? 'danger' : 'gray'),
                TextColumn::make('moderation_review_requested_at')
                    ->label('Peninjauan ulang')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Menunggu admin' : 'Belum diajukan')
                    ->color(fn ($state): string => $state ? 'warning' : 'gray'),
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
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                SelectFilter::make('umkm_id')
                    ->label('UMKM')
                    ->relationship(
                        'umkm',
                        'name',
                        modifyQueryUsing: function (Builder $query): Builder {
                            $user = Filament::auth()->user();

                            if ($user?->isUmkmOwner()) {
                                $query->where('user_id', $user->id);
                            }

                            return $query->orderBy('name');
                        },
                    ),
                TernaryFilter::make('is_active')
                    ->label('Aktif'),
                TernaryFilter::make('is_admin_blocked')
                    ->label('Diblokir admin'),
                TernaryFilter::make('moderation_review_requested_at')
                    ->label('Meminta peninjauan ulang')
                    ->nullable(),
            ])
            ->recordUrl(fn (Product $record): string => Filament::auth()->user()?->isAdmin()
                ? ProductResource::getUrl('view', ['record' => $record])
                : ProductResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('requestReview')
                    ->label('Ajukan peninjauan ulang')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->schema([
                        Textarea::make('note')
                            ->label('Perbaikan yang sudah dilakukan')
                            ->helperText('Jelaskan perubahan produk agar admin dapat meninjaunya kembali.')
                            ->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isUmkmOwner()
                        && $record->umkm?->user_id === Filament::auth()->id()
                        && $record->is_admin_blocked
                        && blank($record->moderation_review_requested_at))
                    ->action(fn (Product $record, array $data) => ContentModeration::requestProductReview($record, Filament::auth()->user(), $data['note'])),
                Action::make('block')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')->label('Alasan penonaktifan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isAdmin() && ! $record->is_admin_blocked)
                    ->action(function (Product $record, array $data): void {
                        ContentModeration::blockProduct($record, Filament::auth()->user(), $data['reason']);

                        Notification::make()
                            ->title('Produk berhasil diblokir')
                            ->body('Produk sudah disembunyikan dari halaman publik dan owner menerima notifikasi dashboard.')
                            ->success()
                            ->send();
                    }),
                Action::make('unblock')
                    ->label('Aktifkan kembali')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->schema([
                        Textarea::make('reason')->label('Catatan pengaktifan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isAdmin() && $record->is_admin_blocked)
                    ->action(function (Product $record, array $data): void {
                        ContentModeration::unblockProduct($record, Filament::auth()->user(), $data['reason']);

                        Notification::make()
                            ->title('Produk berhasil diaktifkan kembali')
                            ->body('Produk dapat tampil lagi sesuai status aktifnya.')
                            ->success()
                            ->send();
                    }),
                Action::make('rejectReview')
                    ->label('Tolak peninjauan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('reason')->label('Alasan masih diblokir')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isAdmin()
                        && $record->is_admin_blocked
                        && filled($record->moderation_review_requested_at))
                    ->action(function (Product $record, array $data): void {
                        ContentModeration::rejectProductReview($record, Filament::auth()->user(), $data['reason']);

                        Notification::make()
                            ->title('Peninjauan produk ditolak')
                            ->body('Produk tetap disembunyikan dan owner menerima catatan keputusan.')
                            ->warning()
                            ->send();
                    }),
                ViewAction::make()->visible(fn (): bool => Filament::auth()->user()?->isAdmin() ?? false),
                EditAction::make()->visible(fn (): bool => Filament::auth()->user()?->isUmkmOwner() ?? false),
            ]);
    }
}
