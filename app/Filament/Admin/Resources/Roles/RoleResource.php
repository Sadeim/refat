<?php

namespace App\Filament\Admin\Resources\Roles;

use App\Filament\Admin\Resources\Roles\Pages\CreateRole;
use App\Filament\Admin\Resources\Roles\Pages\EditRole;
use App\Filament\Admin\Resources\Roles\Pages\ListRoles;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use UnitEnum;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 100;

    public static function getModelLabel(): string { return 'دور'; }
    public static function getPluralModelLabel(): string { return 'الأدوار'; }
    public static function getNavigationLabel(): string { return 'الأدوار'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الدور')->schema([
                TextInput::make('name')->label('الاسم')->required()->unique(ignoreRecord: true),
                TextInput::make('guard_name')->label('Guard')->default('web')->required(),
            ])->columns(2),

            Section::make('الصلاحيات')->schema([
                CheckboxList::make('permissions')
                    ->label('')
                    ->relationship('permissions', 'name')
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('guard_name')->label('Guard')->toggleable(),
                TextColumn::make('permissions_count')->label('عدد الصلاحيات')->counts('permissions')->badge(),
                TextColumn::make('users_count')->label('عدد المستخدمين')->counts('users')->badge(),
            ])
            ->recordActions([EditAction::make()->label('تعديل'), DeleteAction::make()->label('حذف')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
