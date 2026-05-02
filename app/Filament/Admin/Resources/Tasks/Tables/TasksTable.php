<?php

namespace App\Filament\Admin\Resources\Tasks\Tables;

use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('العنوان')->searchable()->sortable()->weight('semibold'),
                TextColumn::make('assignee.name')->label('المسؤول')->searchable(),
                TextColumn::make('due_date')->label('الاستحقاق')->date()->sortable(),
                BadgeColumn::make('priority')->label('الأولوية')
                    ->colors(['gray' => 'low', 'primary' => 'normal', 'warning' => 'high', 'danger' => 'urgent'])
                    ->formatStateUsing(fn (string $state): string => Task::PRIORITIES[$state] ?? $state),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['gray' => 'todo', 'warning' => 'in_progress', 'success' => 'done', 'danger' => 'cancelled'])
                    ->formatStateUsing(fn (string $state): string => Task::STATUSES[$state] ?? $state),
            ])
            ->defaultSort('due_date', 'asc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Task::STATUSES),
                SelectFilter::make('priority')->label('الأولوية')->options(Task::PRIORITIES),
            ])
            ->recordActions([EditAction::make()->label('تعديل')])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
