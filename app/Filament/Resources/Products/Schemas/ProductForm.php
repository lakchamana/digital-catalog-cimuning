<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use App\Support\UniqueSlug;
use App\Support\UploadDisk;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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
                Section::make('Produk dinonaktifkan admin')
                    ->description('Produk tidak tampil ke publik sampai admin membuka blokir setelah perbaikan ditinjau.')
                    ->visible(fn (?Product $record): bool => $record?->is_admin_blocked ?? false)
                    ->schema([
                        Placeholder::make('admin_block_reason_display')
                            ->label('Alasan')
                            ->content(fn (?Product $record): string => $record?->admin_block_reason ?: '-'),
                        Placeholder::make('moderation_review_status_display')
                            ->label('Status peninjauan ulang')
                            ->content(fn (?Product $record): string => $record?->moderation_review_requested_at
                                ? 'Menunggu review admin'
                                : 'Belum diajukan'),
                        Placeholder::make('moderation_review_note_display')
                            ->label('Catatan perbaikan terakhir')
                            ->content(fn (?Product $record): string => $record?->moderation_review_note ?: '-'),
                    ]),
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
                        Hidden::make('slug'),
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
                            ->helperText('Dipakai sebagai foto utama di katalog. Jika kosong, sistem memakai foto galeri pertama.')
                            ->image()
                            ->disk(UploadDisk::name())
                            ->directory('products')
                            ->visibility('public')
                            ->fetchFileInformation(false)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imagePreviewHeight('160')
                            ->openable()
                            ->downloadable(),

                        Repeater::make('images')
                            ->label('Galeri foto produk')
                            ->relationship('images')
                            ->helperText('Tambahkan sampai 6 foto pendukung. Foto paling awal dipakai sebagai cadangan bila gambar utama kosong.')
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Foto')
                                    ->image()
                                    ->disk(UploadDisk::name())
                                    ->directory('products/gallery')
                                    ->visibility('public')
                                    ->fetchFileInformation(false)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(2048)
                                    ->imagePreviewHeight('120')
                                    ->openable()
                                    ->downloadable()
                                    ->required(),
                                TextInput::make('alt_text')
                                    ->label('Keterangan foto')
                                    ->maxLength(255)
                                    ->placeholder('Contoh: varian rasa cokelat'),
                                TextInput::make('sort_order')
                                    ->label('Urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->helperText('Angka kecil tampil lebih dulu.'),
                            ])
                            ->columns(3)
                            ->defaultItems(0)
                            ->maxItems(6)
                            ->addActionLabel('Tambah foto galeri')
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Foto galeri')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
