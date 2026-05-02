<?php

namespace App\Filament\Admin\Resources\FixedExpenses\Tables;

use App\Models\FixedExpense;
use App\Models\Lookup;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FixedExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('category')->label('التصنيف')
                    ->formatStateUsing(fn (?string $state) => Lookup::label(Lookup::TYPE_EXPENSE_CAT, $state, '—'))
                    ->badge(),
                TextColumn::make('amount')->label('المبلغ')->money('ILS')->weight('bold')->sortable(),
                BadgeColumn::make('frequency')->label('التكرار')
                    ->colors(['primary' => 'monthly', 'warning' => 'yearly', 'success' => 'weekly'])
                    ->formatStateUsing(fn (string $state) => FixedExpense::FREQUENCIES[$state] ?? $state),
                TextColumn::make('day_of_period')->label('اليوم')->toggleable(),
                TextColumn::make('next_run_at')->label('الاستحقاق القادم')->date()->sortable()
                    ->color(fn ($record) => $record->next_run_at && $record->next_run_at->isPast() ? 'danger' : null),
                TextColumn::make('last_run_at')->label('آخر تنفيذ')->date()->toggleable(),
                IconColumn::make('is_active')->label('نشط')->boolean(),
                IconColumn::make('auto_post')->label('تلقائي')->boolean()->toggleable(),
            ])
            ->defaultSort('next_run_at', 'asc')
            ->filters([
                SelectFilter::make('frequency')->label('التكرار')->options(FixedExpense::FREQUENCIES),
                SelectFilter::make('is_active')->label('الحالة')
                    ->options([1 => 'نشط', 0 => 'غير نشط']),
            ])
            ->recordActions([
                Action::make('runNow')
                    ->label('تنفيذ الآن')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد إنشاء قيد المصروف')
                    ->modalDescription('سيتم إنشاء حركة مصروف جديدة بالمبلغ المحدد، وتحديث تاريخ الاستحقاق القادم.')
                    ->action(function (FixedExpense $record) {
                        $tx = $record->postTransaction();
                        Notification::make()
                            ->title('تم إنشاء القيد')
                            ->body("تم تسجيل مصروف {$tx->reference_no} بقيمة ".number_format($tx->amount, 2).' ₪')
                            ->success()
                            ->send();
                    }),
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
