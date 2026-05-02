<?php

namespace App\Filament\Admin\Resources\IncomingLetters;

use App\Filament\Admin\Resources\IncomingLetters\Pages\CreateIncomingLetter;
use App\Filament\Admin\Resources\IncomingLetters\Pages\EditIncomingLetter;
use App\Filament\Admin\Resources\IncomingLetters\Pages\ListIncomingLetters;
use App\Filament\Admin\Resources\IncomingLetters\Schemas\IncomingLetterForm;
use App\Filament\Admin\Resources\IncomingLetters\Tables\IncomingLettersTable;
use App\Models\IncomingLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class IncomingLetterResource extends Resource
{
    protected static ?string $model = IncomingLetter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInboxArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'الأرشيف';

    protected static ?int $navigationSort = 30;

    public static function getModelLabel(): string { return 'بريد وارد'; }
    public static function getPluralModelLabel(): string { return 'الوارد'; }
    public static function getNavigationLabel(): string { return 'الوارد'; }

    public static function form(Schema $schema): Schema
    {
        return IncomingLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncomingLettersTable::configure($table);
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
            'index' => ListIncomingLetters::route('/'),
            'create' => CreateIncomingLetter::route('/create'),
            'edit' => EditIncomingLetter::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
