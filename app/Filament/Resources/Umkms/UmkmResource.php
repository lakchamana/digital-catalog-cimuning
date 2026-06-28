<?php

namespace App\Filament\Resources\Umkms;

use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Umkms\Pages\EditUmkm;
use App\Filament\Resources\Umkms\Pages\ListUmkms;
use App\Filament\Resources\Umkms\Pages\ViewUmkm;
use App\Filament\Resources\Umkms\Schemas\UmkmForm;
use App\Filament\Resources\Umkms\Tables\UmkmsTable;
use App\Models\Umkm;
use App\Support\UploadDisk;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class UmkmResource extends Resource
{
    protected static ?string $model = Umkm::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $modelLabel = 'UMKM';

    protected static ?string $pluralModelLabel = 'UMKM';

    protected static ?string $navigationLabel = 'UMKM';

    protected static string|UnitEnum|null $navigationGroup = 'Direktori';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return UmkmForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UmkmsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profil UMKM')
                ->description('Data milik owner ditampilkan read-only. Perubahan harus diajukan oleh owner.')
                ->schema([
                    ImageEntry::make('logo_image')->label('Logo')->disk(UploadDisk::name())->circular(),
                    ImageEntry::make('cover_image')->label('Cover')->disk(UploadDisk::name()),
                    TextEntry::make('name')->label('Nama usaha'),
                    TextEntry::make('category.name')->label('Kategori')->placeholder('-'),
                    TextEntry::make('description')->label('Tentang usaha')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'verified' => 'Terverifikasi',
                            'need_revision' => 'Perlu revisi',
                            'rejected' => 'Ditolak',
                            default => 'Menunggu',
                        }),
                    IconEntry::make('is_active')->label('Tampil publik')->boolean(),
                    IconEntry::make('is_admin_blocked')->label('Diblokir admin')->boolean(),
                    IconEntry::make('is_featured')->label('UMKM pilihan')->boolean(),
                    TextEntry::make('admin_block_reason')->label('Alasan penonaktifan')->placeholder('-')->columnSpanFull(),
                ])->columns(2),
            Section::make('Kontak dan lokasi')
                ->schema([
                    TextEntry::make('owner.name')->label('Akun owner')->placeholder('-'),
                    TextEntry::make('owner_name')->label('Penanggung jawab')->placeholder('-'),
                    TextEntry::make('whatsapp')->label('WhatsApp')->placeholder('-'),
                    TextEntry::make('phone')->label('Telepon')->placeholder('-'),
                    TextEntry::make('email')->label('Email')->placeholder('-'),
                    TextEntry::make('rw')->label('RW')->placeholder('-'),
                    TextEntry::make('address')->label('Alamat')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('instagram')->label('Instagram')->placeholder('-'),
                    TextEntry::make('tiktok')->label('TikTok')->placeholder('-'),
                    TextEntry::make('website')->label('Website')->placeholder('-'),
                ])->columns(2),
            Section::make('Layanan')
                ->schema([
                    IconEntry::make('service_delivery')->label('Delivery')->boolean(),
                    IconEntry::make('service_cod')->label('COD')->boolean(),
                    IconEntry::make('service_custom_order')->label('Custom order')->boolean(),
                    IconEntry::make('has_physical_store')->label('Toko fisik')->boolean(),
                ])->columns(4),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['category', 'owner']);
        $user = Filament::auth()->user();

        if ($user?->isUmkmOwner()) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkms::route('/'),
            'create' => CreateUmkm::route('/create'),
            'view' => ViewUmkm::route('/{record}'),
            'edit' => EditUmkm::route('/{record}/edit'),
        ];
    }
}
