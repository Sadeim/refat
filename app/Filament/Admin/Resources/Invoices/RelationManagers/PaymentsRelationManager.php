<?php

namespace App\Filament\Admin\Resources\Invoices\RelationManagers;

use App\Models\Payment;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'الدفعات';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('amount')->label('المبلغ')->numeric()->prefix('₪')->required(),
            DatePicker::make('paid_at')->label('تاريخ الدفع')->required()->native(false)->default(now()),
            Select::make('method')->label('وسيلة الدفع')->options(Payment::METHODS)->default('cash')->required(),
            TextInput::make('reference')->label('المرجع/رقم الإيصال'),
            Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                TextColumn::make('amount')->label('المبلغ')->money('ILS')->weight('bold'),
                TextColumn::make('paid_at')->label('التاريخ')->date(),
                BadgeColumn::make('method')->label('الوسيلة')->formatStateUsing(fn (string $state): string => Payment::METHODS[$state] ?? $state),
                TextColumn::make('reference')->label('المرجع'),
            ])
            ->headerActions([CreateAction::make()->label('إضافة دفعة')])
            ->recordActions([EditAction::make()->label('تعديل'), DeleteAction::make()->label('حذف')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
