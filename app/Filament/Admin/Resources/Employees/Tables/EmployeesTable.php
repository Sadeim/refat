<?php

namespace App\Filament\Admin\Resources\Employees\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')
                    ->label('الصورة')
                    ->collection('photo')
                    ->conversion('thumb')
                    ->circular(),
                TextColumn::make('code')
                    ->label('الكود')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name_ar')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('position')
                    ->label('المسمى')
                    ->searchable(),
                TextColumn::make('department')
                    ->label('القسم')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('تاريخ المباشرة')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('daily_hours')
                    ->label('ساعات/يوم')
                    ->numeric()
                    ->toggleable(),
                BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'on_leave',
                        'danger' => 'suspended',
                        'gray' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'on_leave' => 'في إجازة',
                        'suspended' => 'موقوف',
                        'terminated' => 'منتهي',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'active' => 'نشط',
                        'on_leave' => 'في إجازة',
                        'suspended' => 'موقوف',
                        'terminated' => 'منتهي',
                    ]),
                SelectFilter::make('department')
                    ->label('القسم'),
                TrashedFilter::make()->label('المحذوفون'),
            ])
            ->recordActions([
                Action::make('card')
                    ->label('بطاقة')
                    ->icon('heroicon-o-identification')
                    ->url(fn ($record) => route('employees.card', $record))
                    ->openUrlInNewTab(),
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
