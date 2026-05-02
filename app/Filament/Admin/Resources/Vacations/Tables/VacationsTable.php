<?php

namespace App\Filament\Admin\Resources\Vacations\Tables;

use App\Models\Vacation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VacationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name_ar')->label('الموظف')->searchable()->sortable(),
                BadgeColumn::make('type')->label('النوع')->formatStateUsing(fn (string $state): string => Vacation::TYPES[$state] ?? $state),
                TextColumn::make('start_date')->label('من')->date()->sortable(),
                TextColumn::make('end_date')->label('إلى')->date()->sortable(),
                TextColumn::make('days')->label('عدد الأيام')->badge(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected', 'gray' => 'cancelled'])
                    ->formatStateUsing(fn (string $state): string => Vacation::STATUSES[$state] ?? $state),
                TextColumn::make('approver.name')->label('المعتمد')->toggleable(),
            ])
            ->defaultSort('start_date', 'desc')
            ->filters([
                SelectFilter::make('type')->label('النوع')->options(Vacation::TYPES),
                SelectFilter::make('status')->label('الحالة')->options(Vacation::STATUSES),
            ])
            ->recordActions([EditAction::make()->label('تعديل')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
