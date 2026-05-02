<?php

namespace App\Filament\Admin\Resources\Transactions\Tables;

use App\Models\Lookup;
use App\Models\Transaction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;

class TransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')->label('الرقم المرجعي')->searchable()->sortable(),
                BadgeColumn::make('type')->label('النوع')
                    ->colors(['success' => 'income', 'danger' => 'expense'])
                    ->formatStateUsing(fn (string $state): string => Transaction::TYPES[$state] ?? $state),
                TextColumn::make('category')->label('التصنيف')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '—';
                        $type = $record->type === 'income' ? Lookup::TYPE_INCOME_CAT : Lookup::TYPE_EXPENSE_CAT;
                        return Lookup::label($type, $state, $state);
                    }),
                TextColumn::make('amount')->label('المبلغ')->money('ILS')->sortable()->weight('bold'),
                TextColumn::make('transaction_date')->label('التاريخ')->date()->sortable(),
                TextColumn::make('party_type')->label('الجهة')
                    ->formatStateUsing(fn (?string $state): string => $state ? (['employee'=>'موظف','customer'=>'عميل'][$state] ?? $state) : '—'),
                TextColumn::make('party.name_ar')->label('الاسم')->toggleable(),
                TextColumn::make('description')->label('الوصف')->limit(40)->toggleable(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['warning' => 'pending', 'success' => 'confirmed', 'danger' => 'cancelled'])
                    ->formatStateUsing(fn (string $state): string => ['pending'=>'قيد التأكيد','confirmed'=>'مؤكدة','cancelled'=>'ملغاة'][$state] ?? $state),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                SelectFilter::make('type')->label('النوع')->options(Transaction::TYPES),
                SelectFilter::make('category')->label('التصنيف')
                    ->options(fn () => Lookup::options(Lookup::TYPE_EXPENSE_CAT) + Lookup::options(Lookup::TYPE_INCOME_CAT)),
                SelectFilter::make('status')->label('الحالة')->options(['pending'=>'قيد التأكيد','confirmed'=>'مؤكدة','cancelled'=>'ملغاة']),
                Filter::make('transaction_date')
                    ->label('فترة التاريخ')
                    ->schema([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '>=', $d))
                            ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('transaction_date', '<=', $d));
                    }),
                TrashedFilter::make()->label('المحذوفون'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
