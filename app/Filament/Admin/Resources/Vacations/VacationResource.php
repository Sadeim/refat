<?php

namespace App\Filament\Admin\Resources\Vacations;

use App\Filament\Admin\Resources\Vacations\Pages\CreateVacation;
use App\Filament\Admin\Resources\Vacations\Pages\EditVacation;
use App\Filament\Admin\Resources\Vacations\Pages\ListVacations;
use App\Filament\Admin\Resources\Vacations\Schemas\VacationForm;
use App\Filament\Admin\Resources\Vacations\Tables\VacationsTable;
use App\Models\Vacation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class VacationResource extends Resource
{
    protected static ?string $model = Vacation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSun;

    protected static string|UnitEnum|null $navigationGroup = 'الموارد البشرية';

    protected static ?int $navigationSort = 11;

    public static function getModelLabel(): string { return 'إجازة'; }
    public static function getPluralModelLabel(): string { return 'الإجازات'; }
    public static function getNavigationLabel(): string { return 'الإجازات'; }

    public static function form(Schema $schema): Schema
    {
        return VacationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VacationsTable::configure($table);
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
            'index' => ListVacations::route('/'),
            'create' => CreateVacation::route('/create'),
            'edit' => EditVacation::route('/{record}/edit'),
        ];
    }
}
