<?php

namespace App\Filament\Resources\Umkms\Schemas;

use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UploadDisk;
use App\Support\UniqueSlug;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\View;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class UmkmForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make([
                    Step::make('Informasi usaha')
                        ->schema(self::profileSchema()),
                    Step::make('Kontak')
                        ->schema(self::contactSchema()),
                    Step::make('Lokasi')
                        ->schema(self::locationSchema()),
                    Step::make('Media sosial')
                        ->schema(self::socialSchema()),
                    Step::make('Foto & layanan')
                        ->schema(self::mediaAndServicesSchema()),
                    Step::make('Konfirmasi')
                        ->schema(self::reviewSchema()),
                ])
                    ->columnSpanFull()
                    ->nextAction(fn ($action) => $action->label('Lanjut'))
                    ->previousAction(fn ($action) => $action->label('Kembali')),
            ]);
    }

    /**
     * @return array<int, Component>
     */
    private static function profileSchema(): array
    {
        return [
            Section::make('Informasi usaha')
                ->schema([
                    Select::make('category_id')
                        ->label('Kategori')
                        ->relationship('category', 'name', modifyQueryUsing: fn ($query) => $query->where('is_active', true)->orderBy('name'))
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('name')
                        ->label('Nama usaha')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: Dapur Ibu Sari')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state, ?Model $record) => $set(
                            'slug',
                            UniqueSlug::make((string) $state, Umkm::class, ignoreId: $record?->getKey()),
                        )),
                    Hidden::make('slug'),
                    Textarea::make('description')
                        ->label('Tentang usaha')
                        ->placeholder('Ceritakan produk atau jasa utama, jam buka, dan keunggulan usaha Anda.')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function contactSchema(): array
    {
        return [
            Section::make('Kontak')
                ->schema([
                    TextInput::make('owner_name')
                        ->label('Nama pemilik atau penanggung jawab')
                        ->placeholder('Nama lengkap')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('Telepon')
                        ->tel()
                        ->placeholder('Contoh: 02182601234')
                        ->maxLength(255),
                    TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Contoh: 081234567890')
                        ->helperText('Gunakan nomor yang aktif menerima pesan WhatsApp.'),
                    TextInput::make('email')
                        ->label('Email usaha')
                        ->email()
                        ->placeholder('Contoh: usaha@email.com')
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function locationSchema(): array
    {
        return [
            Section::make('Lokasi usaha')
                ->schema([
                    Select::make('rw')
                        ->label('RW')
                        ->options(Umkm::rwOptions())
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->searchPrompt('Ketik nomor RW')
                        ->noSearchResultsMessage('RW tidak ditemukan')
                        ->required(),
                    TextInput::make('maps_link')
                        ->label('Link lokasi Google Maps')
                        ->placeholder('Tempel link lokasi usaha dari Google Maps')
                        ->live(onBlur: true)
                        ->helperText('Buka lokasi usaha di Google Maps, lalu salin dan tempel linknya di sini.')
                        ->afterStateUpdated(function (Set $set, ?string $state): void {
                            $coordinates = OwnerFormHelper::coordinatesFromMapsText($state);

                            if (! $coordinates) {
                                return;
                            }

                            $set('latitude', $coordinates['latitude']);
                            $set('longitude', $coordinates['longitude']);
                        }),
                    View::make('filament.forms.components.location-helper')
                        ->columnSpanFull(),
                    Hidden::make('latitude'),
                    Hidden::make('longitude'),
                    Textarea::make('address')
                        ->label('Alamat lengkap')
                        ->placeholder('Tulis nama jalan, nomor, dan patokan yang mudah ditemukan.')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function socialSchema(): array
    {
        return [
            Section::make('Media sosial')
                ->schema([
                    TextInput::make('instagram')
                        ->label('Instagram')
                        ->placeholder('Contoh: @dapurcimuning')
                        ->maxLength(255),
                    TextInput::make('tiktok')
                        ->label('TikTok')
                        ->placeholder('Contoh: @dapurcimuning')
                        ->maxLength(255),
                    TextInput::make('website')
                        ->label('Website')
                        ->url()
                        ->placeholder('https://contohusaha.id')
                        ->maxLength(255),
                ])
                ->columns(2),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function mediaAndServicesSchema(): array
    {
        return [
            Section::make('Foto usaha')
                ->schema([
                    FileUpload::make('logo_image')
                        ->label('Logo usaha')
                        ->helperText('Opsional. Gunakan gambar JPG, PNG, atau WEBP maksimal 2 MB.')
                        ->image()
                        ->disk(UploadDisk::name())
                        ->directory('umkms/logos')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->imagePreviewHeight('120')
                        ->openable()
                        ->downloadable(),
                    FileUpload::make('cover_image')
                        ->label('Cover usaha')
                        ->helperText('Opsional. Foto tempat usaha atau produk utama akan terlihat di profil.')
                        ->image()
                        ->disk(UploadDisk::name())
                        ->directory('umkms/covers')
                        ->visibility('public')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(2048)
                        ->imagePreviewHeight('140')
                        ->openable()
                        ->downloadable(),
                ])
                ->columns(2),

            Section::make('Layanan')
                ->schema([
                    Toggle::make('service_delivery')
                        ->label('Bisa delivery')
                        ->default(false)
                        ->required(),
                    Toggle::make('service_cod')
                        ->label('Bisa COD')
                        ->default(false)
                        ->required(),
                    Toggle::make('service_custom_order')
                        ->label('Menerima custom order')
                        ->default(false)
                        ->required(),
                    Toggle::make('has_physical_store')
                        ->label('Punya toko fisik')
                        ->default(false)
                        ->required(),
                ])
                ->columns(2),
        ];
    }

    /**
     * @return array<int, Component>
     */
    private static function reviewSchema(): array
    {
        return [
            Section::make('Konfirmasi')
                ->schema([
                    View::make('filament.forms.components.umkm-submit-note'),
                ]),
        ];
    }
}
