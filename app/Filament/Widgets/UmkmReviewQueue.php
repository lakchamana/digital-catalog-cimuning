<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Umkms\UmkmResource;
use App\Models\Umkm;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class UmkmReviewQueue extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('UMKM perlu ditinjau')
            ->description('Pendaftaran baru dan profil yang membutuhkan tindak lanjut admin.')
            ->query(
                Umkm::query()
                    ->with(['category', 'owner'])
                    ->whereIn('status', ['pending', 'need_revision'])
                    ->latest(),
            )
            ->columns([
                TextColumn::make('name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('owner_name')
                    ->label('Penanggung jawab')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'need_revision' => 'Perlu revisi',
                        default => 'Menunggu',
                    })
                    ->color(fn (string $state): string => $state === 'need_revision' ? 'warning' : 'gray'),
                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (Umkm $record): string => UmkmResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('Tidak ada UMKM yang perlu ditinjau')
            ->emptyStateDescription('Pendaftaran baru dan revisi akan muncul di sini.');
    }
}
