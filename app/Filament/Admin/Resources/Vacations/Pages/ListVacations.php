<?php

namespace App\Filament\Admin\Resources\Vacations\Pages;

use App\Filament\Admin\Resources\Vacations\VacationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVacations extends ListRecords
{
    protected static string $resource = VacationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
