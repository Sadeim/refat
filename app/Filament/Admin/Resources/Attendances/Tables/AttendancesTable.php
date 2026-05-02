<?php

namespace App\Filament\Admin\Resources\Attendances\Tables;

use App\Models\Attendance;
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
                TextColumn::make('check_in')->label('الدخول')->time('H:i'),
                TextColumn::make('check_out')->label('الخروج')->time('H:i'),
                TextColumn::make('hours')->label('الساعات')->badge(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['success' => 'present', 'warning' => 'late', 'danger' => 'absent', 'gray' => 'half_day', 'primary' => 'leave'])
                    ->formatStateUsing(fn (string $state): string => Attendance::STATUSES[$state] ?? $state),
                TextColumn::make('check_in_method')->label('الطريقة')
                    ->formatStateUsing(fn (?string $state): string => ['manual'=>'يدوي','qr'=>'QR','fingerprint'=>'بصمة'][$state] ?? '—')
                    ->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Attendance::STATUSES),
            ])
            ->recordActions([EditAction::make()->label('تعديل')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
