<?php

namespace App\Filament\Admin\Resources\Salaries;

use App\Filament\Admin\Resources\Salaries\Pages\CreateSalary;
use App\Filament\Admin\Resources\Salaries\Pages\EditSalary;
use App\Filament\Admin\Resources\Salaries\Pages\ListSalaries;
use App\Filament\Admin\Resources\Salaries\Schemas\SalaryForm;
use App\Filament\Admin\Resources\Salaries\Tables\SalariesTable;
use App\Models\Salary;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SalaryResource extends Resource
{
    protected static ?string $model = Salary::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'المحاسبة';

    protected static ?int $navigationSort = 50;

    public static function getModelLabel(): string { return 'راتب'; }
    public static function getPluralModelLabel(): string { return 'الرواتب'; }
    public static function getNavigationLabel(): string { return 'الرواتب'; }

    public static function form(Schema $schema): Schema
    {
        return SalaryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalariesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSalaries::route('/'),
            'create' => CreateSalary::route('/create'),
            'edit' => EditSalary::route('/{record}/edit'),
        ];
    }
}
