<?php

namespace App\Filament\Admin\Resources\Vacations\Pages;

use App\Filament\Admin\Resources\Vacations\VacationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVacation extends EditRecord
{
    protected static string $resource = VacationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
