<?php

namespace App\Filament\Admin\Resources\VehicleTrips\Pages;

use App\Filament\Admin\Resources\VehicleTrips\VehicleTripResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleTrips extends ListRecords
{
    protected static string $resource = VehicleTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
