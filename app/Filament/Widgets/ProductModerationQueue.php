<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class ProductModerationQueue extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isAdmin() ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Produk menunggu peninjauan ulang')
            ->description('Produk yang telah diperbaiki owner dan masih diblokir dari katalog publik.')
            ->query(Product::query()
                ->with(['umkm.owner'])
                ->where('is_admin_blocked', true)
                ->whereNotNull('moderation_review_requested_at')
                ->latest('moderation_review_requested_at'))
            ->columns([
                TextColumn::make('name')->label('Produk')->searchable(),
                TextColumn::make('umkm.name')->label('UMKM')->searchable(),
                TextColumn::make('moderation_review_note')->label('Perbaikan owner')->wrap()->limit(100),
                TextColumn::make('moderation_review_requested_at')->label('Diajukan')->since()->sortable(),
            ])
            ->recordUrl(fn (Product $record): string => ProductResource::getUrl('view', ['record' => $record]))
            ->emptyStateHeading('Tidak ada produk yang menunggu review')
            ->emptyStateDescription('Permintaan peninjauan ulang dari owner akan muncul di sini.');
    }
}
