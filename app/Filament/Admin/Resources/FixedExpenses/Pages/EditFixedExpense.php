<?php

namespace App\Filament\Admin\Resources\FixedExpenses\Pages;

use App\Filament\Admin\Resources\FixedExpenses\FixedExpenseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFixedExpense extends EditRecord
{
    protected static string $resource = FixedExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
