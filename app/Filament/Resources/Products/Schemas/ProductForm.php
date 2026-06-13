<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use App\Support\UniqueSlug;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi produk')
                    ->schema([
                        Select::make('umkm_id')
                            ->label('UMKM')
                            ->relationship(
                                'umkm',
                                'name',
                                modifyQueryUsing: function (Builder $query): Builder {
                                    $query->orderBy('name');
                                    $user = Filament::auth()->user();

                                    if ($user?->isUmkmOwner()) {
                                        $query->where('user_id', $user->id);
                                    }

                                    return $query;
                                },
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', modifyQueryUsing: fn (Builder $query): Builder => $query->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama produk')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, ?Model $record) => $set(
                                'slug',
                                UniqueSlug::make((string) $state, Product::class, ignoreId: $record?->getKey()),
                            )),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Slug dipakai untuk referensi URL/SEO tahap berikutnya.'),
                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->placeholder('25000'),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Foto produk')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Gambar utama')
                            ->image()
                            ->disk('public')
                            ->directory('products')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imagePreviewHeight('160')
                            ->openable()
                            ->downloadable(),
                    ]),
            ]);
    }
}
