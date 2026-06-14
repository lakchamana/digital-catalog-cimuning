<?php

namespace App\Filament\Resources\Umkms\Schemas;

use App\Models\Umkm;
use App\Support\OwnerFormHelper;
use App\Support\UploadDisk;
use App\Support\UniqueSlug;
use Filament\Facades\Filament;
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
                    Step::make('Profil usaha')
                        ->description('Isi informasi dasar yang akan tampil di website.')
                        ->schema(self::profileSchema()),
                    Step::make('Kontak')
                        ->description('Tambahkan kontak yang bisa dihubungi warga.')
                        ->schema(self::contactSchema()),
                    Step::make('Lokasi')
                        ->description('Isi alamat dan bantu sistem menemukan titik Maps.')
                        ->schema(self::locationSchema()),
                    Step::make('Media sosial')
                        ->description('Opsional, boleh isi username atau link.')
                        ->schema(self::socialSchema()),
                    Step::make('Foto & layanan')
                        ->description('Lengkapi tampilan profil dan layanan usaha.')
                        ->schema(self::mediaAndServicesSchema()),
                    Step::make('Tinjauan')
                        ->description('Pastikan data siap dikirim untuk ditinjau admin.')
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
                        ->preload()
                        ->required()
                        ->helperText('Pilih kategori yang paling dekat dengan usaha Anda.'),
                    TextInput::make('name')
                        ->label('Nama UMKM')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Nama usaha yang akan tampil di website.')
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state, ?Model $record) => $set(
                            'slug',
                            UniqueSlug::make((string) $state, Umkm::class, ignoreId: $record?->getKey()),
                        )),
                    TextInput::make('slug')
                        ->label('Slug URL publik')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Untuk admin: bagian URL publik, contoh: dapur-ibu-sari.')
                        ->visible(fn () => Filament::auth()->user()?->isAdmin()),
                    Hidden::make('slug')
                        ->visible(fn () => Filament::auth()->user()?->isUmkmOwner()),
                    Textarea::make('description')
                        ->label('Deskripsi')
                        ->helperText('Ceritakan produk/jasa utama, jam layanan, atau keunggulan usaha.')
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
                        ->label('Nama penanggung jawab')
                        ->helperText('Nama orang yang bisa dihubungi admin atau warga.')
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label('Telepon')
                        ->tel()
                        ->maxLength(255),
                    TextInput::make('whatsapp')
                        ->label('WhatsApp')
                        ->tel()
                        ->maxLength(255)
                        ->helperText('Gunakan nomor aktif, contoh: 081234567890. Tombol publik akan mengarah ke WhatsApp.'),
                    TextInput::make('email')
                        ->label('Email usaha')
                        ->email()
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
                    TextInput::make('rw')
                        ->label('RW')
                        ->maxLength(10)
                        ->placeholder('RW 03'),
                    TextInput::make('maps_link')
                        ->label('Tempel link Google Maps atau koordinat')
                        ->live(onBlur: true)
                        ->helperText('Contoh koordinat: -6.312345, 107.012345. Jika menempel link Google Maps pendek, buka link dulu lalu salin alamat lengkap dari browser.')
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
                    TextInput::make('latitude')
                        ->label('Latitude Google Maps')
                        ->numeric()
                        ->helperText('Terisi otomatis jika memakai tombol lokasi atau link Maps.'),
                    TextInput::make('longitude')
                        ->label('Longitude Google Maps')
                        ->numeric()
                        ->helperText('Terisi otomatis jika memakai tombol lokasi atau link Maps.'),
                    Textarea::make('address')
                        ->label('Alamat')
                        ->helperText('Tulis alamat yang mudah dipahami warga. Contoh: dekat masjid, sekolah, atau patokan sekitar.')
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
                        ->helperText('Boleh isi username, contoh: dapurcimuning, atau link lengkap.')
                        ->maxLength(255),
                    TextInput::make('tiktok')
                        ->label('TikTok')
                        ->helperText('Boleh isi username, contoh: dapurcimuning, atau link lengkap.')
                        ->maxLength(255),
                    TextInput::make('website')
                        ->label('Website')
                        ->url()
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
            Section::make('Status publik')
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
                        ->required()
                        ->disabled(fn () => ! Filament::auth()->user()?->isAdmin())
                        ->dehydrated(),
                    Toggle::make('is_featured')
                        ->label('UMKM pilihan')
                        ->default(false)
                        ->visible(fn () => Filament::auth()->user()?->isAdmin())
                        ->dehydrated(),
                    TextInput::make('view_count')
                        ->label('Jumlah dilihat')
                        ->required()
                        ->numeric()
                        ->default(0)
                        ->disabled(fn () => ! Filament::auth()->user()?->isAdmin())
                        ->dehydrated(),
                ])
                ->columns(2),
            Section::make('Sebelum dikirim')
                ->schema([
                    View::make('filament.forms.components.umkm-submit-note'),
                ]),
        ];
    }
}
