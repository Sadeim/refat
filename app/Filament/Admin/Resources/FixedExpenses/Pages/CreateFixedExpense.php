<?php

namespace App\Filament\Admin\Resources\FixedExpenses\Pages;

use App\Filament\Admin\Resources\FixedExpenses\FixedExpenseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFixedExpense extends CreateRecord
{
    protected static string $resource = FixedExpenseResource::class;
}
