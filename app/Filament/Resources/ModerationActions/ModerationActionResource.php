<?php

namespace App\Filament\Resources\ModerationActions;

use App\Filament\Resources\ModerationActions\Pages\ListModerationActions;
use App\Filament\Resources\ModerationActions\Pages\ViewModerationAction;
use App\Models\ModerationAction;
use App\Models\Product;
use App\Models\Umkm;
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

class ModerationActionResource extends Resource
{
    protected static ?string $model = ModerationAction::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Log Moderasi';

    protected static ?string $modelLabel = 'Aktivitas Moderasi';

    protected static ?string $pluralModelLabel = 'Log Moderasi';

    protected static string|UnitEnum|null $navigationGroup = 'Verifikasi';

    protected static ?int $navigationSort = 3;

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
                TextColumn::make('actor.name')->label('Aktor')->placeholder('Sistem')->searchable(),
                TextColumn::make('subject_type')->label('Jenis konten')->badge()
                    ->formatStateUsing(fn (string $state): string => $state === Product::class ? 'Produk' : 'UMKM'),
                TextColumn::make('subject_name')->label('Konten')
                    ->state(fn (ModerationAction $record): string => $record->subject?->name ?? 'Data sudah tidak tersedia')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $nested) use ($search): void {
                            $nested->whereHasMorph('subject', [Product::class, Umkm::class], fn (Builder $subject) => $subject->where('name', 'like', "%{$search}%"));
                        });
                    }),
                TextColumn::make('action')->label('Aksi')->badge()
                    ->formatStateUsing(fn (string $state): string => self::actionLabel($state))
                    ->color(fn (string $state): string => self::actionColor($state)),
                TextColumn::make('reason')->label('Alasan/catatan')->placeholder('-')->wrap()->limit(80),
            ])
            ->filters([
                SelectFilter::make('action')->label('Aksi')->options(self::actionOptions()),
                SelectFilter::make('actor_id')->label('Aktor')->relationship('actor', 'name')->searchable()->preload(),
                SelectFilter::make('subject_type')->label('Jenis konten')->options([
                    Product::class => 'Produk',
                    Umkm::class => 'UMKM',
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
            ->recordUrl(fn (ModerationAction $record): string => static::getUrl('view', ['record' => $record]));
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Aktivitas moderasi')
                ->description('Catatan ini bersifat read-only dan tidak dapat diubah dari dashboard.')
                ->schema([
                    TextEntry::make('created_at')->label('Waktu')->dateTime(),
                    TextEntry::make('actor.name')->label('Aktor')->placeholder('Sistem'),
                    TextEntry::make('subject_type')->label('Jenis konten')
                        ->formatStateUsing(fn (string $state): string => $state === Product::class ? 'Produk' : 'UMKM'),
                    TextEntry::make('subject_name')->label('Konten')
                        ->state(fn (ModerationAction $record): string => $record->subject?->name ?? 'Data sudah tidak tersedia'),
                    TextEntry::make('action')->label('Aksi')->badge()
                        ->formatStateUsing(fn (string $state): string => self::actionLabel($state))
                        ->color(fn (string $state): string => self::actionColor($state)),
                    TextEntry::make('reason')->label('Alasan/catatan')->placeholder('-')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModerationActions::route('/'),
            'view' => ViewModerationAction::route('/{record}'),
        ];
    }

    public static function actionOptions(): array
    {
        return [
            'featured' => 'Dijadikan UMKM pilihan',
            'unfeatured' => 'Dihapus dari UMKM pilihan',
            'blocked' => 'Produk diblokir',
            'review_requested' => 'Peninjauan diminta owner',
            'review_rejected' => 'Peninjauan ditolak',
            'unblocked' => 'Blokir produk dibuka',
        ];
    }

    private static function actionLabel(string $action): string
    {
        return self::actionOptions()[$action] ?? $action;
    }

    private static function actionColor(string $action): string
    {
        return match ($action) {
            'featured', 'unblocked' => 'success',
            'blocked', 'review_rejected' => 'danger',
            'review_requested' => 'warning',
            default => 'gray',
        };
    }
}
