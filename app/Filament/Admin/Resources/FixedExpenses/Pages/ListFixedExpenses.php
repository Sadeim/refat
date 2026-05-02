<?php

namespace App\Filament\Admin\Resources\FixedExpenses\Pages;

use App\Filament\Admin\Resources\FixedExpenses\FixedExpenseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFixedExpenses extends ListRecords
{
    protected static string $resource = FixedExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
