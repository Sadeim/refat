<?php

namespace App\Filament\Admin\Resources\Vehicles;

use App\Filament\Admin\Resources\Vehicles\Pages\CreateVehicle;
use App\Filament\Admin\Resources\Vehicles\Pages\EditVehicle;
use App\Filament\Admin\Resources\Vehicles\Pages\ListVehicles;
use App\Filament\Admin\Resources\Vehicles\RelationManagers\TripsRelationManager;
use App\Filament\Admin\Resources\Vehicles\Schemas\VehicleForm;
use App\Filament\Admin\Resources\Vehicles\Tables\VehiclesTable;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'المركبات';

    protected static ?int $navigationSort = 10;

    public static function getModelLabel(): string { return 'مركبة'; }
    public static function getPluralModelLabel(): string { return 'المركبات'; }
    public static function getNavigationLabel(): string { return 'المركبات'; }

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            TripsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'edit' => EditVehicle::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
