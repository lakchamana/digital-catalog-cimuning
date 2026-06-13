<?php

namespace App\Filament\Resources\Umkms\Schemas;

use App\Models\Umkm;
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
use Illuminate\Database\Eloquent\Model;

class UmkmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil usaha')
                    ->schema([
                        Select::make('user_id')
                            ->label('Pemilik akun')
                            ->relationship(
                                'owner',
                                'name',
                                modifyQueryUsing: fn ($query) => $query->where('role', 'umkm_owner')->orderBy('name'),
                            )
                            ->searchable()
                            ->preload()
                            ->default(fn () => Filament::auth()->user()?->isUmkmOwner() ? Filament::auth()->id() : null)
                            ->disabled(fn () => Filament::auth()->user()?->isUmkmOwner())
                            ->dehydrated()
                            ->visible(fn () => Filament::auth()->user()?->isAdmin()),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'))
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama UMKM')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state, ?Model $record) => $set(
                                'slug',
                                UniqueSlug::make((string) $state, Umkm::class, ignoreId: $record?->getKey()),
                            )),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Slug dipakai untuk URL publik, contoh: dapur-ibu-sari.'),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Kontak dan lokasi')
                    ->schema([
                        TextInput::make('owner_name')
                            ->label('Nama penanggung jawab')
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(255),
                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(255)
                            ->helperText('Gunakan nomor aktif. Website publik akan mengarah ke wa.me.'),
                        TextInput::make('email')
                            ->label('Email usaha')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('rw')
                            ->label('RW')
                            ->maxLength(10)
                            ->placeholder('RW 03'),
                        TextInput::make('latitude')
                            ->label('Latitude Google Maps')
                            ->numeric(),
                        TextInput::make('longitude')
                            ->label('Longitude Google Maps')
                            ->numeric(),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Media dan kanal digital')
                    ->schema([
                        FileUpload::make('logo_image')
                            ->label('Logo usaha')
                            ->image()
                            ->disk('public')
                            ->directory('umkms/logos')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imagePreviewHeight('120')
                            ->openable()
                            ->downloadable(),
                        FileUpload::make('cover_image')
                            ->label('Cover usaha')
                            ->image()
                            ->disk('public')
                            ->directory('umkms/covers')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->imagePreviewHeight('140')
                            ->openable()
                            ->downloadable(),
                        TextInput::make('instagram')
                            ->label('Instagram')
                            ->maxLength(255),
                        TextInput::make('tiktok')
                            ->label('TikTok')
                            ->maxLength(255),
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Status publik dan layanan')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Menunggu verifikasi',
                                'verified' => 'Terverifikasi',
                                'rejected' => 'Ditolak',
                                'need_revision' => 'Perlu revisi',
                            ])
                            ->default('pending')
                            ->required()
                            ->disabled(fn () => ! Filament::auth()->user()?->isAdmin())
                            ->dehydrated(),
                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->required(),
                        Toggle::make('is_featured')
                            ->label('UMKM pilihan')
                            ->default(false)
                            ->visible(fn () => Filament::auth()->user()?->isAdmin())
                            ->dehydrated(),
                        Toggle::make('service_delivery')
                            ->label('Delivery')
                            ->default(false)
                            ->required(),
                        Toggle::make('service_cod')
                            ->label('COD')
                            ->default(false)
                            ->required(),
                        Toggle::make('service_custom_order')
                            ->label('Custom order')
                            ->default(false)
                            ->required(),
                        Toggle::make('has_physical_store')
                            ->label('Toko fisik')
                            ->default(false)
                            ->required(),
                        TextInput::make('view_count')
                            ->label('Jumlah dilihat')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->disabled(fn () => ! Filament::auth()->user()?->isAdmin())
                            ->dehydrated(),
                    ])
                    ->columns(2),
            ]);
    }
}
