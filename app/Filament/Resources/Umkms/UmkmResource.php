<?php

namespace App\Filament\Resources\Umkms;

use App\Filament\Resources\Umkms\Pages\CreateUmkm;
use App\Filament\Resources\Umkms\Pages\EditUmkm;
use App\Filament\Resources\Umkms\Pages\ListUmkms;
use App\Filament\Resources\Umkms\Schemas\UmkmForm;
use App\Filament\Resources\Umkms\Tables\UmkmsTable;
use App\Models\Umkm;
use BackedEnum;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
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
            'edit' => EditUmkm::route('/{record}/edit'),
        ];
    }
}
