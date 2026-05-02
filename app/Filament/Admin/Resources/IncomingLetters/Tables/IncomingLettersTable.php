<?php

namespace App\Filament\Admin\Resources\IncomingLetters\Tables;

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

class IncomingLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')->label('الرقم المرجعي')->searchable()->sortable(),
                TextColumn::make('letter_date')->label('تاريخ الكتاب')->date()->sortable(),
                TextColumn::make('from_party')->label('من')->searchable(),
                TextColumn::make('subject')->label('الموضوع')->searchable()->limit(50),
                BadgeColumn::make('priority')->label('الأولوية')
                    ->colors(['gray' => 'low', 'primary' => 'normal', 'warning' => 'high', 'danger' => 'urgent'])
                    ->formatStateUsing(fn (string $state): string => ['low'=>'منخفضة','normal'=>'عادية','high'=>'عالية','urgent'=>'عاجل'][$state] ?? $state),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['warning'=>'open','primary'=>'in_progress','success'=>'closed','gray'=>'archived'])
                    ->formatStateUsing(fn (string $state): string => ['open'=>'مفتوح','in_progress'=>'قيد المعالجة','closed'=>'مغلق','archived'=>'مؤرشف'][$state] ?? $state),
                TextColumn::make('assignedTo.name')->label('المسؤول')->toggleable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(['open'=>'مفتوح','in_progress'=>'قيد المعالجة','closed'=>'مغلق','archived'=>'مؤرشف']),
                SelectFilter::make('priority')->label('الأولوية')->options(['low'=>'منخفضة','normal'=>'عادية','high'=>'عالية','urgent'=>'عاجل']),
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
