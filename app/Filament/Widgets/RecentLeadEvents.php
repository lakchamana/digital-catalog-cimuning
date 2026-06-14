<?php

namespace App\Filament\Widgets;

use App\Models\LeadEvent;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentLeadEvents extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Aktivitas lead terbaru')
            ->description('Klik WhatsApp dan Maps dari halaman publik.')
            ->query(
                LeadEvent::query()
                    ->visibleTo(Filament::auth()->user())
                    ->with(['umkm', 'product'])
                    ->latest(),
            )
            ->columns([
                TextColumn::make('umkm.name')
                    ->label('UMKM')
                    ->searchable(),
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'whatsapp' => 'WhatsApp',
                        'maps' => 'Maps',
                        'qr_scan' => 'Scan QR',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'maps', 'qr_scan' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->label('Sumber')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'detail' => 'Detail',
                        'card' => 'Card',
                        'product_card' => 'Card produk',
                        'sticky' => 'Sticky mobile',
                        'maps_section' => 'Section Maps',
                        'qr_profile' => 'QR profil',
                        default => '-',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->emptyStateHeading('Belum ada lead')
            ->emptyStateDescription('Klik WhatsApp, Maps, dan scan QR dari halaman publik akan muncul di sini.');
    }
}
