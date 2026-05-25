<?php

namespace App\Filament\Admin\Resources\VehicleTrips;

use App\Filament\Admin\Resources\VehicleTrips\Pages\CreateVehicleTrip;
use App\Filament\Admin\Resources\VehicleTrips\Pages\EditVehicleTrip;
use App\Filament\Admin\Resources\VehicleTrips\Pages\ListVehicleTrips;
use App\Filament\Admin\Resources\VehicleTrips\Schemas\VehicleTripForm;
use App\Filament\Admin\Resources\VehicleTrips\Tables\VehicleTripsTable;
use App\Models\VehicleTrip;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VehicleTripResource extends Resource
{
    protected static ?string $model = VehicleTrip::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMap;

    protected static string|UnitEnum|null $navigationGroup = 'المركبات';

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string { return 'رحلة'; }
    public static function getPluralModelLabel(): string { return 'سجل الحركات'; }
    public static function getNavigationLabel(): string { return 'سجل الحركات'; }

    public static function form(Schema $schema): Schema
    {
        return VehicleTripForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehicleTripsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVehicleTrips::route('/'),
            'create' => CreateVehicleTrip::route('/create'),
            'edit' => EditVehicleTrip::route('/{record}/edit'),
        ];
    }
}
