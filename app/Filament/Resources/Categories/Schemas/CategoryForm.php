<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Models\Category;
use App\Support\UniqueSlug;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi kategori')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama kategori')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, ?Model $record) => $set(
                                'slug',
                                UniqueSlug::make((string) $state, Category::class, ignoreId: $record?->getKey()),
                            )),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Gunakan huruf kecil dan tanda hubung, contoh: produk-kreatif.'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->columnSpanFull(),
                        TextInput::make('icon')
                            ->label('Ikon singkat')
                            ->maxLength(255)
                            ->helperText('Opsional, bisa berupa nama ikon atau teks pendek untuk referensi UI publik.'),
                        TextInput::make('sort_order')
                            ->label('Urutan tampil')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }
}
