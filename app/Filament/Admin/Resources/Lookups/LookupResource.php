<?php

namespace App\Filament\Admin\Resources\Lookups;

use App\Filament\Admin\Resources\Lookups\Pages\ManageLookups;
use App\Models\Lookup;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class LookupResource extends Resource
{
    protected static ?string $model = Lookup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 95;

    public static function getModelLabel(): string { return 'قائمة'; }
    public static function getPluralModelLabel(): string { return 'القوائم والتصنيفات'; }
    public static function getNavigationLabel(): string { return 'القوائم والتصنيفات'; }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('التصنيف')->columns(2)->schema([
                    Select::make('type')->label('نوع القائمة')
                        ->options(Lookup::TYPE_LABELS)
                        ->required()
                        ->searchable(),
                    TextInput::make('key')->label('المفتاح (إنجليزي بدون مسافات)')
                        ->required()->regex('/^[a-z0-9_]+$/')->maxLength(80),
                    TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                    TextInput::make('label_en')->label('الاسم بالإنجليزي'),
                    TextInput::make('icon')->label('Heroicon (اختياري)')->placeholder('heroicon-o-shield-check'),
                    ColorPicker::make('color')->label('اللون (اختياري)'),
                    TextInput::make('sort')->label('الترتيب')->numeric()->default(0),
                    Toggle::make('is_active')->label('نشط')->default(true),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                BadgeColumn::make('type')->label('القائمة')
                    ->formatStateUsing(fn (?string $state) => Lookup::TYPE_LABELS[$state] ?? $state)
                    ->colors([
                        'primary' => Lookup::TYPE_CUSTOMER,
                        'warning' => Lookup::TYPE_CUSTODY,
                        'danger'  => Lookup::TYPE_EXPENSE_CAT,
                        'success' => Lookup::TYPE_INCOME_CAT,
                        'info'    => Lookup::TYPE_INVOICE_CAT,
                    ]),
                TextColumn::make('key')->label('المفتاح')->searchable()->fontFamily('mono')->size('sm'),
                TextColumn::make('label_ar')->label('الاسم')->searchable()->weight('semibold'),
                TextColumn::make('label_en')->label('English')->toggleable(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
                TextColumn::make('sort')->label('الترتيب')->sortable(),
            ])
            ->defaultSort('type')
            ->filters([
                SelectFilter::make('type')->label('القائمة')->options(Lookup::TYPE_LABELS),
                SelectFilter::make('is_active')->label('الحالة')
                    ->options([1 => 'نشط', 0 => 'غير نشط']),
            ])
            ->groups(['type'])
            ->defaultGroup('type')
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLookups::route('/'),
        ];
    }
}
