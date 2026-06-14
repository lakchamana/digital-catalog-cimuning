<?php

namespace App\Filament\Resources\Products\Tables;

use App\Models\Product;
use App\Support\UploadDisk;
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
            ->recordActions([
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
