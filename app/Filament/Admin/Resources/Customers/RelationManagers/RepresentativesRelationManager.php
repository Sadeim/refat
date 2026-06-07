<?php

namespace App\Filament\Admin\Resources\Customers\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RepresentativesRelationManager extends RelationManager
{
    protected static string $relationship = 'representatives';

    protected static ?string $title = 'الممثلون عن الجهة';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-user-group';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الممثل')->columns(2)->schema([
                TextInput::make('name')->label('اسم الممثل')->required(),
                TextInput::make('position')->label('المنصب / المسمى الوظيفي')
                    ->placeholder('مدير، مسؤول مالي، نائب رئيس...'),
                TextInput::make('national_id')->label('رقم الهوية'),
                TextInput::make('phone')->label('الهاتف الأساسي')->tel(),
                TextInput::make('phone_alt')->label('هاتف بديل')->tel(),
                TextInput::make('whatsapp')->label('واتساب')->tel(),
                TextInput::make('email')->label('البريد الإلكتروني')->email(),
                Toggle::make('is_primary')->label('الممثل الرئيسي')
                    ->helperText('عند التفعيل، يُلغى عن أي ممثل آخر في نفس الجهة'),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull()->rows(2),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IconColumn::make('is_primary')->label('رئيسي')->boolean()
                    ->trueIcon('heroicon-s-star')->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),
                TextColumn::make('name')->label('الاسم')->searchable()->weight('semibold'),
                TextColumn::make('position')->label('المنصب')->searchable()->toggleable(),
                TextColumn::make('phone')->label('الهاتف')->searchable()->copyable(),
                TextColumn::make('whatsapp')->label('واتساب')->copyable()->toggleable(),
                TextColumn::make('email')->label('البريد الإلكتروني')->copyable()->toggleable(),
                TextColumn::make('national_id')->label('رقم الهوية')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('is_primary', 'desc')
            ->headerActions([
                CreateAction::make()->label('+ إضافة ممثل'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
