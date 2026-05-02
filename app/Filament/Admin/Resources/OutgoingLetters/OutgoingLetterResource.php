<?php

namespace App\Filament\Admin\Resources\OutgoingLetters;

use App\Filament\Admin\Resources\OutgoingLetters\Pages\CreateOutgoingLetter;
use App\Filament\Admin\Resources\OutgoingLetters\Pages\EditOutgoingLetter;
use App\Filament\Admin\Resources\OutgoingLetters\Pages\ListOutgoingLetters;
use App\Filament\Admin\Resources\OutgoingLetters\Schemas\OutgoingLetterForm;
use App\Filament\Admin\Resources\OutgoingLetters\Tables\OutgoingLettersTable;
use App\Models\OutgoingLetter;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class OutgoingLetterResource extends Resource
{
    protected static ?string $model = OutgoingLetter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static string|UnitEnum|null $navigationGroup = 'الأرشيف';

    protected static ?int $navigationSort = 31;

    public static function getModelLabel(): string { return 'بريد صادر'; }
    public static function getPluralModelLabel(): string { return 'الصادر'; }
    public static function getNavigationLabel(): string { return 'الصادر'; }

    public static function form(Schema $schema): Schema
    {
        return OutgoingLetterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutgoingLettersTable::configure($table);
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
            'index' => ListOutgoingLetters::route('/'),
            'create' => CreateOutgoingLetter::route('/create'),
            'edit' => EditOutgoingLetter::route('/{record}/edit'),
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
