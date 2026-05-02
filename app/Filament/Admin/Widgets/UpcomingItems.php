<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingItems extends TableWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): ?string
    {
        return 'عقود تنتهي قريباً (خلال 60 يوم)';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn (): Builder => Customer::query()
                    ->whereNotNull('contract_end')
                    ->whereBetween('contract_end', [today(), today()->addDays(60)])
                    ->orderBy('contract_end')
            )
            ->columns([
                TextColumn::make('code')->label('الكود'),
                TextColumn::make('name_ar')->label('العميل'),
                TextColumn::make('contract_end')->label('ينتهي في')->date()
                    ->color(fn ($record) => $record->contract_end <= today()->addDays(15) ? 'danger' : 'warning'),
                TextColumn::make('days_left')->label('متبقي')
                    ->state(fn ($record) => today()->diffInDays($record->contract_end).' يوم'),
                TextColumn::make('contract_value')->label('قيمة العقد')->money('ILS'),
            ])
            ->paginated(false);
    }
}
