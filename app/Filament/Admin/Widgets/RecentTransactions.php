<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentTransactions extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function getTableHeading(): ?string
    {
        return 'آخر الحركات المالية';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Transaction::query()->latest('transaction_date')->limit(10))
            ->columns([
                TextColumn::make('reference_no')->label('الرقم'),
                BadgeColumn::make('type')->label('النوع')
                    ->colors(['success' => 'income', 'danger' => 'expense'])
                    ->formatStateUsing(fn (string $state): string => Transaction::TYPES[$state] ?? $state),
                TextColumn::make('amount')->label('المبلغ')->money('ILS'),
                TextColumn::make('transaction_date')->label('التاريخ')->date(),
                TextColumn::make('description')->label('الوصف')->limit(40),
            ])
            ->paginated(false);
    }
}
