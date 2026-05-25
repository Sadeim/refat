<?php

namespace App\Filament\Admin\Resources\VehicleTrips\Pages;

use App\Filament\Admin\Resources\VehicleTrips\VehicleTripResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleTrip extends EditRecord
{
    protected static string $resource = VehicleTripResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
