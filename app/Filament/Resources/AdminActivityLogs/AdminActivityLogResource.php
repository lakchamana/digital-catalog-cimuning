<?php

namespace App\Filament\Resources\AdminActivityLogs;

use App\Filament\Resources\AdminActivityLogs\Pages\ListAdminActivityLogs;
use App\Filament\Resources\AdminActivityLogs\Pages\ViewAdminActivityLog;
use App\Models\AdminActivityLog;
use App\Models\BackupRun;
use App\Models\Category;
use App\Models\RestoreRequest;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AdminActivityLogResource extends Resource
{
    protected static ?string $model = AdminActivityLog::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Log Aktivitas Admin';

    protected static ?string $modelLabel = 'Aktivitas Admin';

    protected static ?string $pluralModelLabel = 'Log Aktivitas Admin';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    protected static ?int $navigationSort = 4;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['actor', 'subject']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Waktu')->dateTime()->sortable(),
                TextColumn::make('actor.name')->label('Aktor')->placeholder('Tidak terautentikasi')->searchable(),
                TextColumn::make('event')->label('Aktivitas')->badge()
                    ->formatStateUsing(fn (string $state): string => self::eventLabel($state))
                    ->color(fn (string $state): string => self::eventColor($state)),
                TextColumn::make('subject_label')->label('Target')->placeholder('-')->searchable(),
                TextColumn::make('metadata.route')->label('Route')->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reason')->label('Catatan')->placeholder('-')->limit(80)->wrap(),
                TextColumn::make('request_id')->label('Request ID')->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('event')->label('Aktivitas')->options(self::eventOptions()),
                SelectFilter::make('actor_id')->label('Aktor')->relationship('actor', 'name')->searchable()->preload(),
                SelectFilter::make('subject_type')->label('Jenis target')->options([
                    Category::class => 'Kategori',
                    User::class => 'Akun',
                    BackupRun::class => 'Backup database',
                    RestoreRequest::class => 'Permintaan restore',
                ]),
                Filter::make('created_at')
                    ->label('Rentang waktu')
                    ->schema([
                        DatePicker::make('from')->label('Dari tanggal'),
                        DatePicker::make('until')->label('Sampai tanggal'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))),
            ])
            ->recordUrl(fn (AdminActivityLog $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Aktivitas admin')
                ->description('Log ini read-only. Password, token, secret, IP mentah, dan isi media tidak disimpan.')
                ->schema([
                    TextEntry::make('created_at')->label('Waktu')->dateTime(),
                    TextEntry::make('actor.name')->label('Aktor')->placeholder('Tidak terautentikasi'),
                    TextEntry::make('event')->label('Aktivitas')->badge()
                        ->formatStateUsing(fn (string $state): string => self::eventLabel($state))
                        ->color(fn (string $state): string => self::eventColor($state)),
                    TextEntry::make('subject_label')->label('Target')->placeholder('-'),
                    TextEntry::make('reason')->label('Catatan')->placeholder('-')->columnSpanFull(),
                    TextEntry::make('request_id')->label('Request ID')->copyable()->columnSpanFull(),
                ])->columns(2),
            Section::make('Perubahan aman')
                ->schema([
                    TextEntry::make('before')->label('Sebelum')->placeholder('-')
                        ->state(fn (AdminActivityLog $record): string => self::formatJson($record->before)),
                    TextEntry::make('after')->label('Sesudah')->placeholder('-')
                        ->state(fn (AdminActivityLog $record): string => self::formatJson($record->after)),
                    TextEntry::make('metadata')->label('Metadata request')->placeholder('-')->columnSpanFull()
                        ->state(fn (AdminActivityLog $record): string => self::formatJson($record->metadata)),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdminActivityLogs::route('/'),
            'view' => ViewAdminActivityLog::route('/{record}'),
        ];
    }

    public static function eventOptions(): array
    {
        return [
            'admin_login' => 'Admin login',
            'admin_logout' => 'Admin logout',
            'admin_login_failed' => 'Login panel gagal',
            'admin_access_denied' => 'Akses sensitif ditolak',
            'admin_profile_updated' => 'Profil admin diperbarui',
            'category_created' => 'Kategori dibuat',
            'category_updated' => 'Kategori diperbarui',
            'category_deleted' => 'Kategori dihapus',
            'database_backup_started' => 'Backup database dimulai',
            'database_backup_completed' => 'Backup database selesai',
            'database_backup_failed' => 'Backup database gagal',
            'database_backup_downloaded' => 'Backup database diunduh',
            'restore_request_created' => 'Permintaan restore dibuat',
            'restore_validation_failed' => 'Validasi restore gagal',
        ];
    }

    private static function eventLabel(string $event): string
    {
        return self::eventOptions()[$event] ?? $event;
    }

    private static function eventColor(string $event): string
    {
        return match ($event) {
            'admin_login', 'category_created', 'database_backup_completed' => 'success',
            'admin_login_failed', 'admin_access_denied', 'category_deleted', 'database_backup_failed', 'restore_validation_failed' => 'danger',
            'admin_logout', 'category_updated', 'admin_profile_updated', 'database_backup_downloaded', 'restore_request_created' => 'info',
            'database_backup_started' => 'warning',
            default => 'gray',
        };
    }

    private static function formatJson(?array $state): string
    {
        return $state === null || $state === []
            ? '-'
            : (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
