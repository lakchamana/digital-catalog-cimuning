<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Support\UploadDisk;
use App\Support\ContentModeration;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
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
            ])
            ->recordUrl(fn (Product $record): string => Filament::auth()->user()?->isAdmin()
                ? \App\Filament\Resources\Products\ProductResource::getUrl('view', ['record' => $record])
                : \App\Filament\Resources\Products\ProductResource::getUrl('edit', ['record' => $record]))
            ->recordActions([
                Action::make('block')
                    ->label('Nonaktifkan')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->schema([
                        Textarea::make('reason')->label('Alasan penonaktifan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isAdmin() && ! $record->is_admin_blocked)
                    ->action(fn (Product $record, array $data) => ContentModeration::blockProduct($record, Filament::auth()->user(), $data['reason'])),
                Action::make('unblock')
                    ->label('Aktifkan kembali')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->schema([
                        Textarea::make('reason')->label('Catatan pengaktifan')->required()->minLength(10)->maxLength(2000),
                    ])
                    ->visible(fn (Product $record): bool => Filament::auth()->user()?->isAdmin() && $record->is_admin_blocked)
                    ->action(fn (Product $record, array $data) => ContentModeration::unblockProduct($record, Filament::auth()->user(), $data['reason'])),
                ViewAction::make()->visible(fn (): bool => Filament::auth()->user()?->isAdmin() ?? false),
                EditAction::make()->visible(fn (): bool => Filament::auth()->user()?->isUmkmOwner() ?? false),
            ]);
    }
}
