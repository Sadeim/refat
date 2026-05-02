<?php

namespace App\Filament\Admin\Resources\OutgoingLetters\Tables;

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

class OutgoingLettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')->label('الرقم المرجعي')->searchable()->sortable(),
                TextColumn::make('letter_date')->label('تاريخ الكتاب')->date()->sortable(),
                TextColumn::make('to_party')->label('إلى')->searchable(),
                TextColumn::make('subject')->label('الموضوع')->searchable()->limit(50),
                BadgeColumn::make('priority')->label('الأولوية')
                    ->colors(['gray' => 'low', 'primary' => 'normal', 'warning' => 'high', 'danger' => 'urgent'])
                    ->formatStateUsing(fn (string $state): string => ['low'=>'منخفضة','normal'=>'عادية','high'=>'عالية','urgent'=>'عاجل'][$state] ?? $state),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['gray' => 'draft', 'primary' => 'sent', 'success' => 'delivered', 'warning' => 'archived'])
                    ->formatStateUsing(fn (string $state): string => ['draft'=>'مسودة','sent'=>'مُرسل','delivered'=>'مُستلم','archived'=>'مؤرشف'][$state] ?? $state),
                TextColumn::make('createdBy.name')->label('بواسطة')->toggleable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(['draft'=>'مسودة','sent'=>'مُرسل','delivered'=>'مُستلم','archived'=>'مؤرشف']),
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
