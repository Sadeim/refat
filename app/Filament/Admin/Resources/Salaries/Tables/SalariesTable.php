<?php

namespace App\Filament\Admin\Resources\Salaries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SalariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name_ar')->label('الموظف')->searchable()->sortable(),
                TextColumn::make('year')->label('السنة')->sortable(),
                TextColumn::make('month')->label('الشهر')->sortable()
                    ->formatStateUsing(fn ($state) => [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'][$state] ?? $state),
                TextColumn::make('basic')->label('الأساسي')->money('ILS'),
                TextColumn::make('allowances')->label('البدلات')->money('ILS')->toggleable(),
                TextColumn::make('overtime')->label('إضافي')->money('ILS')->toggleable(),
                TextColumn::make('advances')->label('سُلَف')->money('ILS')->toggleable(),
                TextColumn::make('deductions')->label('خصومات')->money('ILS')->toggleable(),
                TextColumn::make('net')->label('الصافي')->money('ILS')->weight('bold'),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['gray' => 'draft', 'warning' => 'approved', 'success' => 'paid'])
                    ->formatStateUsing(fn (string $state): string => ['draft'=>'مسودة','approved'=>'معتمد','paid'=>'مدفوع'][$state] ?? $state),
                TextColumn::make('paid_at')->label('تاريخ الصرف')->date()->toggleable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(['draft'=>'مسودة','approved'=>'معتمد','paid'=>'مدفوع']),
                SelectFilter::make('year')->label('السنة')->options(collect(range(now()->year - 3, now()->year + 1))->mapWithKeys(fn ($y) => [$y => $y])->all()),
                SelectFilter::make('month')->label('الشهر')->options([1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر']),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
