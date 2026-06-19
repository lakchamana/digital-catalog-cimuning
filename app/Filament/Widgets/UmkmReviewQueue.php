<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UmkmSubmissions\UmkmSubmissionResource;
use App\Models\UmkmSubmission;
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
                UmkmSubmission::query()
                    ->with(['umkm.category', 'umkm.owner'])
                    ->where('status', 'pending')
                    ->latest('submitted_at'),
            )
            ->columns([
                TextColumn::make('umkm.name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('umkm.category.name')
                    ->label('Kategori'),
                TextColumn::make('umkm.owner_name')
                    ->label('Penanggung jawab')
                    ->toggleable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        default => 'Menunggu review',
                    })
                    ->color('gray'),
                TextColumn::make('submitted_at')
                    ->label('Masuk')
                    ->since()
                    ->sortable(),
            ])
            ->recordUrl(fn (UmkmSubmission $record): string => UmkmSubmissionResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('Tidak ada UMKM yang perlu ditinjau')
            ->emptyStateDescription('Pendaftaran baru dan revisi akan muncul di sini.');
    }
}
