<?php

namespace App\Filament\Resources\UmkmSubmissions;

use App\Filament\Resources\UmkmSubmissions\Pages\ListUmkmSubmissions;
use App\Filament\Resources\UmkmSubmissions\Pages\ViewUmkmSubmission;
use App\Models\Category;
use App\Models\UmkmSubmission;
use App\Support\UploadDisk;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class UmkmSubmissionResource extends Resource
{
    protected static ?string $model = UmkmSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Verifikasi UMKM';

    protected static ?string $modelLabel = 'Pengajuan UMKM';

    protected static ?string $pluralModelLabel = 'Verifikasi UMKM';

    protected static string|UnitEnum|null $navigationGroup = 'Verifikasi';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('submitted_at', 'desc')
            ->columns([
                TextColumn::make('umkm.name')->label('UMKM')->searchable(),
                TextColumn::make('umkm.owner.name')->label('Owner')->searchable(),
                TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'update' ? 'Perubahan profil' : 'Pendaftaran baru')
                    ->color(fn (string $state): string => $state === 'update' ? 'info' : 'gray'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                    ->color(fn (string $state): string => self::statusColor($state)),
                TextColumn::make('submitted_at')->label('Diajukan')->since()->sortable(),
                TextColumn::make('reviewer.name')->label('Reviewer')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    'pending' => 'Menunggu review',
                    'approved' => 'Disetujui',
                    'need_revision' => 'Perlu revisi',
                    'rejected' => 'Ditolak',
                    'superseded' => 'Digantikan',
                ]),
                SelectFilter::make('type')->options([
                    'initial' => 'Pendaftaran baru',
                    'update' => 'Perubahan profil',
                ]),
            ])
            ->recordUrl(fn (UmkmSubmission $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Status pengajuan')
                ->description('Admin hanya menilai data yang diajukan. Data owner tidak dapat diedit dari halaman ini.')
                ->schema([
                    TextEntry::make('status')->label('Status')->badge()
                        ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                        ->color(fn (string $state): string => self::statusColor($state)),
                    TextEntry::make('type')->label('Jenis')
                        ->formatStateUsing(fn (string $state): string => $state === 'update' ? 'Perubahan profil' : 'Pendaftaran baru'),
                    TextEntry::make('submitter.name')->label('Diajukan oleh')->placeholder('-'),
                    TextEntry::make('submitted_at')->label('Waktu pengajuan')->dateTime(),
                    TextEntry::make('reviewer.name')->label('Ditinjau oleh')->placeholder('-'),
                    TextEntry::make('reviewed_at')->label('Waktu keputusan')->dateTime()->placeholder('-'),
                    TextEntry::make('review_notes')->label('Catatan keputusan')->placeholder('Belum ada catatan')->columnSpanFull(),
                ])->columns(3),

            Section::make('Data yang diajukan owner')
                ->schema(self::profileEntries('payload.'))
                ->columns(2),

            Section::make('Ringkasan perubahan')
                ->description('Field berikut berbeda dari profil yang sedang tayang.')
                ->visible(fn (UmkmSubmission $record): bool => $record->type === 'update')
                ->schema([
                    TextEntry::make('changed_fields')
                        ->label('Perubahan terdeteksi')
                        ->state(fn (UmkmSubmission $record): string => self::changedFieldLabels($record))
                        ->placeholder('Tidak ada perubahan nilai yang terdeteksi.'),
                ]),

            Section::make('Versi publik saat ini')
                ->description('Data ini tetap tayang sampai perubahan di atas disetujui.')
                ->visible(fn (UmkmSubmission $record): bool => $record->type === 'update')
                ->schema(self::profileEntries('umkm.'))
                ->columns(2),
        ]);
    }

    private static function profileEntries(string $prefix): array
    {
        $payload = $prefix === 'payload.';

        return [
            ImageEntry::make($prefix.'logo_image')->label('Logo')->disk(UploadDisk::name())->circular(),
            ImageEntry::make($prefix.'cover_image')->label('Cover')->disk(UploadDisk::name()),
            TextEntry::make($prefix.'name')->label('Nama usaha')->placeholder('-'),
            TextEntry::make($prefix.'category_id')->label('Kategori')
                ->formatStateUsing(fn ($state): string => $payload
                    ? (Category::query()->find($state)?->name ?? '-')
                    : (Category::query()->find($state)?->name ?? '-')),
            TextEntry::make($prefix.'description')->label('Tentang usaha')->placeholder('-')->columnSpanFull(),
            TextEntry::make($prefix.'owner_name')->label('Penanggung jawab')->placeholder('-'),
            TextEntry::make($prefix.'whatsapp')->label('WhatsApp')->placeholder('-'),
            TextEntry::make($prefix.'phone')->label('Telepon')->placeholder('-'),
            TextEntry::make($prefix.'email')->label('Email')->placeholder('-'),
            TextEntry::make($prefix.'rw')->label('RW')->placeholder('-'),
            TextEntry::make($prefix.'address')->label('Alamat')->placeholder('-')->columnSpanFull(),
            TextEntry::make($prefix.'latitude')->label('Latitude')->placeholder('-'),
            TextEntry::make($prefix.'longitude')->label('Longitude')->placeholder('-'),
            TextEntry::make($prefix.'instagram')->label('Instagram')->placeholder('-'),
            TextEntry::make($prefix.'tiktok')->label('TikTok')->placeholder('-'),
            TextEntry::make($prefix.'website')->label('Website')->placeholder('-'),
            IconEntry::make($prefix.'service_delivery')->label('Delivery')->boolean(),
            IconEntry::make($prefix.'service_cod')->label('COD')->boolean(),
            IconEntry::make($prefix.'service_custom_order')->label('Custom order')->boolean(),
            IconEntry::make($prefix.'has_physical_store')->label('Toko fisik')->boolean(),
        ];
    }

    private static function changedFieldLabels(UmkmSubmission $submission): string
    {
        $labels = [
            'category_id' => 'Kategori', 'name' => 'Nama usaha', 'description' => 'Tentang usaha',
            'owner_name' => 'Penanggung jawab', 'phone' => 'Telepon', 'whatsapp' => 'WhatsApp',
            'email' => 'Email', 'address' => 'Alamat', 'rw' => 'RW', 'latitude' => 'Latitude',
            'longitude' => 'Longitude', 'instagram' => 'Instagram', 'tiktok' => 'TikTok',
            'website' => 'Website', 'cover_image' => 'Cover', 'logo_image' => 'Logo',
            'service_delivery' => 'Delivery', 'service_cod' => 'COD',
            'service_custom_order' => 'Custom order', 'has_physical_store' => 'Toko fisik',
        ];

        return collect($labels)
            ->filter(fn (string $label, string $field): bool => ($submission->payload[$field] ?? null) != $submission->umkm->getAttribute($field))
            ->values()
            ->implode(', ');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUmkmSubmissions::route('/'),
            'view' => ViewUmkmSubmission::route('/{record}'),
        ];
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            'approved' => 'Disetujui',
            'need_revision' => 'Perlu revisi',
            'rejected' => 'Ditolak',
            'superseded' => 'Digantikan pengajuan baru',
            default => 'Menunggu review',
        };
    }

    private static function statusColor(string $status): string
    {
        return match ($status) {
            'approved' => 'success',
            'need_revision' => 'warning',
            'rejected' => 'danger',
            default => 'gray',
        };
    }
}
