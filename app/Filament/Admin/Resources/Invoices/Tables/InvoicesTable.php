<?php

namespace App\Filament\Admin\Resources\Invoices\Tables;

use App\Models\Invoice;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('number')->label('الرقم')->searchable()->sortable(),
                TextColumn::make('customer.name_ar')->label('العميل')->searchable()->sortable(),
                TextColumn::make('issue_date')->label('الإصدار')->date()->sortable(),
                TextColumn::make('due_date')->label('الاستحقاق')->date()->sortable(),
                TextColumn::make('total')->label('الإجمالي')->money('ILS')->weight('bold'),
                TextColumn::make('paid_total')->label('المدفوع')->money('ILS')->color('success'),
                TextColumn::make('remaining')->label('المتبقي')->money('ILS')->color('danger')
                    ->state(fn ($record) => $record->total - $record->paid_total),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['gray' => 'draft', 'primary' => 'sent', 'warning' => 'partial', 'success' => 'paid', 'danger' => 'overdue', 'gray' => 'cancelled'])
                    ->formatStateUsing(fn (string $state): string => Invoice::STATUSES[$state] ?? $state),
            ])
            ->defaultSort('issue_date', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Invoice::STATUSES),
                TrashedFilter::make()->label('المحذوفة'),
            ])
            ->recordActions([EditAction::make()->label('تعديل')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make(), ForceDeleteBulkAction::make(), RestoreBulkAction::make()])]);
    }
}
