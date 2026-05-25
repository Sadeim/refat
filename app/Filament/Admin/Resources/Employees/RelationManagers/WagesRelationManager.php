<?php

namespace App\Filament\Admin\Resources\Employees\RelationManagers;

use App\Models\Attendance;
use App\Models\WagePayment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WagesRelationManager extends RelationManager
{
    protected static string $relationship = 'wagePayments';

    protected static ?string $title = 'تقرير الأجور الشهرية';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-banknotes';

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    /**
     * On mount/refresh: scan attendances for this employee and create/update
     * wage_payment rows for every (year, month) that has attendance data.
     */
    public function mount(): void
    {
        parent::mount();
        $this->refreshFromAttendances();
    }

    protected function refreshFromAttendances(): void
    {
        $employee = $this->getOwnerRecord();
        if (!$employee) return;

        $months = Attendance::where('employee_id', $employee->id)
            ->selectRaw('strftime("%Y", date) as y, strftime("%m", date) as m')
            ->groupBy('y', 'm')
            ->get();

        foreach ($months as $m) {
            WagePayment::buildFromAttendances($employee->id, (int) $m->y, (int) $m->m);
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('period_label')->label('الشهر')->weight('bold'),
                TextColumn::make('work_days')->label('أيام دوام')->badge()->color('success'),
                TextColumn::make('absence_days')->label('غياب')->badge()->color('danger'),
                TextColumn::make('leave_days')->label('إجازة')->badge()->color('warning'),
                TextColumn::make('total_hours')->label('إجمالي الساعات')->suffix(' س')->weight('semibold'),
                TextColumn::make('total_amount')->label('إجمالي الأجر')->money('ILS')->weight('bold')->color('primary'),
                BadgeColumn::make('paid_at')->label('الحالة')
                    ->formatStateUsing(fn ($state) => $state ? 'مدفوع — '.\Carbon\Carbon::parse($state)->format('Y-m-d') : 'غير مدفوع')
                    ->colors(['success' => fn ($state) => filled($state), 'danger' => fn ($state) => blank($state)]),
                TextColumn::make('transaction.reference_no')->label('رقم القيد')->toggleable(),
                TextColumn::make('paidBy.name')->label('دفع بواسطة')->toggleable(),
            ])
            ->defaultSort('year', 'desc')
            ->modifyQueryUsing(fn ($query) => $query->orderBy('year', 'desc')->orderBy('month', 'desc'))
            ->filters([
                SelectFilter::make('paid_at')
                    ->label('الحالة')
                    ->options(['paid' => 'مدفوع', 'unpaid' => 'غير مدفوع'])
                    ->query(fn ($q, array $data) => match ($data['value'] ?? null) {
                        'paid' => $q->whereNotNull('paid_at'),
                        'unpaid' => $q->whereNull('paid_at'),
                        default => $q,
                    }),
            ])
            ->headerActions([
                Action::make('refresh')
                    ->label('تحديث من الحضور')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->action(function () {
                        $this->refreshFromAttendances();
                        Notification::make()->title('تم تحديث الأرقام من سجلات الحضور')->success()->send();
                    }),
            ])
            ->recordActions([
                Action::make('pay')
                    ->label('💵 تأكيد الدفع')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (WagePayment $record) => is_null($record->paid_at) && $record->total_amount > 0)
                    ->schema([
                        Select::make('payment_method')->label('طريقة الدفع')
                            ->options(['cash' => 'نقداً', 'bank' => 'تحويل بنكي', 'cheque' => 'شيك', 'card' => 'بطاقة'])
                            ->default('cash')
                            ->required(),
                        Textarea::make('notes')->label('ملاحظات (اختياري)')->rows(2),
                    ])
                    ->modalHeading(fn (WagePayment $record) => 'تأكيد دفع أجر '.$record->period_label)
                    ->modalDescription(fn (WagePayment $record) => 'سيتم تسجيل قيد مصروف بقيمة '.number_format($record->total_amount, 2).' ₪ وأرشفة الدفعة.')
                    ->action(function (WagePayment $record, array $data) {
                        $record->markAsPaid($data['payment_method'] ?? 'cash', $data['notes'] ?? null);
                        Notification::make()
                            ->title('تم تسجيل الدفعة')
                            ->body('قيد المصروف: '.$record->fresh()->transaction?->reference_no)
                            ->success()->send();
                    }),

                Action::make('undoPayment')
                    ->label('إلغاء الدفع')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (WagePayment $record) => filled($record->paid_at))
                    ->action(function (WagePayment $record) {
                        $record->transaction?->delete();
                        $record->update([
                            'paid_at' => null, 'payment_method' => null,
                            'transaction_id' => null, 'paid_by' => null,
                        ]);
                        Notification::make()->title('تم إلغاء الدفعة وحذف القيد المرتبط')->warning()->send();
                    }),

                Action::make('view')
                    ->label('تفاصيل')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn (WagePayment $record) => 'تفاصيل أجر '.$record->period_label)
                    ->modalContent(fn (WagePayment $record) => view('filament.employees.wage-details', [
                        'wage' => $record,
                        'attendances' => Attendance::where('employee_id', $record->employee_id)
                            ->whereYear('date', $record->year)
                            ->whereMonth('date', $record->month)
                            ->orderBy('date')->get(),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('إغلاق'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
