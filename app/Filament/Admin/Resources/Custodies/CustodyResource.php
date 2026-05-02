<?php

namespace App\Filament\Admin\Resources\Custodies;

use App\Filament\Admin\Resources\Custodies\Pages\CreateCustody;
use App\Filament\Admin\Resources\Custodies\Pages\EditCustody;
use App\Filament\Admin\Resources\Custodies\Pages\ListCustodies;
use App\Filament\Admin\Resources\Custodies\Schemas\CustodyForm;
use App\Filament\Admin\Resources\Custodies\Tables\CustodiesTable;
use App\Models\Custody;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class CustodyResource extends Resource
{
    protected static ?string $model = Custody::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'العهد والمقتنيات';

    protected static ?int $navigationSort = 40;

    public static function getModelLabel(): string { return 'عهدة'; }
    public static function getPluralModelLabel(): string { return 'العهد'; }
    public static function getNavigationLabel(): string { return 'العهد والمقتنيات'; }

    public static function form(Schema $schema): Schema
    {
        return CustodyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustodiesTable::configure($table);
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
            'index' => ListCustodies::route('/'),
            'create' => CreateCustody::route('/create'),
            'edit' => EditCustody::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
