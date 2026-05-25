<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\Attendance;
use App\Models\Lookup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('التاريخ')->date()->sortable(),
                TextColumn::make('employee.name_ar')->label('الموظف')->searchable()->sortable(),
                TextColumn::make('work_location')->label('مكان العمل')
                    ->formatStateUsing(fn (?string $state) => Lookup::label('work_location', $state, $state))
                    ->toggleable(),
                BadgeColumn::make('period')->label('الفترة')
                    ->formatStateUsing(fn (?string $state) => Attendance::PERIODS[$state] ?? $state)
                    ->toggleable(),
                TextColumn::make('hours')->label('الساعات')->badge()->suffix(' س'),
                TextColumn::make('hourly_rate')->label('سعر الساعة')->money('ILS')->toggleable(),
                TextColumn::make('daily_total')->label('الإجمالي اليومي')->money('ILS')->weight('bold')->sortable(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['success' => 'present', 'warning' => 'late', 'danger' => 'absent', 'gray' => 'half_day', 'primary' => 'leave'])
                    ->formatStateUsing(fn (string $state): string => Attendance::STATUSES[$state] ?? $state),
                TextColumn::make('supervisor.name')->label('المشرف')->toggleable(),
                TextColumn::make('supervisor_notes')->label('ملاحظات المشرف')->limit(30)->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Attendance::STATUSES),
                SelectFilter::make('period')->label('الفترة')->options(Attendance::PERIODS),
                SelectFilter::make('work_location')->label('مكان العمل')->options(fn () => Lookup::options('work_location')),
            ])
            ->recordActions([EditAction::make()->label('تعديل')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
