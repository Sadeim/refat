<?php

namespace App\Filament\Admin\Resources\FixedExpenses;

use App\Filament\Admin\Resources\FixedExpenses\Pages\CreateFixedExpense;
use App\Filament\Admin\Resources\FixedExpenses\Pages\EditFixedExpense;
use App\Filament\Admin\Resources\FixedExpenses\Pages\ListFixedExpenses;
use App\Filament\Admin\Resources\FixedExpenses\Schemas\FixedExpenseForm;
use App\Filament\Admin\Resources\FixedExpenses\Tables\FixedExpensesTable;
use App\Models\FixedExpense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FixedExpenseResource extends Resource
{
    protected static ?string $model = FixedExpense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'المحاسبة';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string { return 'مصروف ثابت'; }
    public static function getPluralModelLabel(): string { return 'المصاريف الثابتة'; }
    public static function getNavigationLabel(): string { return 'المصاريف الثابتة'; }

    public static function form(Schema $schema): Schema
    {
        return FixedExpenseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FixedExpensesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFixedExpenses::route('/'),
            'create' => CreateFixedExpense::route('/create'),
            'edit' => EditFixedExpense::route('/{record}/edit'),
        ];
    }
}
