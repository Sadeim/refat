<?php

namespace App\Filament\Admin\Resources\Customers\RelationManagers;

use App\Models\CustomerService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    protected static ?string $title = 'الخدمات';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_type')
                    ->label('نوع الخدمة')
                    ->options(CustomerService::TYPES)
                    ->required(),
                TextInput::make('title')
                    ->label('عنوان الخدمة')
                    ->columnSpanFull(),
                DatePicker::make('start_date')->label('تاريخ البداية')->native(false),
                DatePicker::make('end_date')->label('تاريخ النهاية')->native(false),
                TextInput::make('amount')->label('المبلغ')->numeric()->prefix('₪')->default(0),
                Select::make('status')->label('الحالة')->options([
                    'active' => 'نشطة', 'completed' => 'منتهية', 'cancelled' => 'ملغاة',
                ])->default('active'),
                KeyValue::make('details')
                    ->label('التفاصيل (لوحة سيارة، رقم سلاح، اسم محمي ...)')
                    ->keyLabel('الحقل')
                    ->valueLabel('القيمة')
                    ->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                BadgeColumn::make('service_type')->label('النوع')
                    ->formatStateUsing(fn (string $state): string => CustomerService::TYPES[$state] ?? $state),
                TextColumn::make('title')->label('العنوان')->searchable(),
                TextColumn::make('start_date')->label('من')->date(),
                TextColumn::make('end_date')->label('إلى')->date(),
                TextColumn::make('amount')->label('المبلغ')->money('ILS'),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['success' => 'active', 'gray' => 'completed', 'danger' => 'cancelled']),
            ])
            ->headerActions([
                CreateAction::make()->label('إضافة خدمة'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
