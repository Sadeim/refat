<?php

namespace App\Filament\Admin\Resources\Custodies\Tables;

use App\Models\Custody;
use App\Models\Lookup;
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

class CustodiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference_no')->label('رقم العهدة')->searchable()->sortable(),
                BadgeColumn::make('asset_type')->label('النوع')
                    ->formatStateUsing(fn (?string $state): string => Lookup::label(Lookup::TYPE_CUSTODY, $state, $state)),
                TextColumn::make('asset_name')->label('الاسم')->searchable(),
                TextColumn::make('serial_no')->label('الرقم التسلسلي')->searchable()->toggleable(),
                TextColumn::make('value')->label('القيمة')->money('ILS')->sortable(),
                TextColumn::make('assigned_to_type')->label('المسلَّم إليه')
                    ->formatStateUsing(fn (string $state): string => ['employee'=>'موظف','customer'=>'عميل'][$state] ?? $state),
                TextColumn::make('assignedTo.name_ar')->label('الاسم'),
                TextColumn::make('delivered_at')->label('تاريخ التسليم')->date()->sortable(),
                TextColumn::make('returned_at')->label('تاريخ الاسترجاع')->date()->sortable()->toggleable(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['warning' => 'delivered', 'success' => 'returned', 'danger' => 'lost', 'gray' => 'damaged'])
                    ->formatStateUsing(fn (string $state): string => ['delivered'=>'مسلَّمة','returned'=>'مُستردَّة','lost'=>'مفقودة','damaged'=>'تالفة'][$state] ?? $state),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('asset_type')->label('النوع')->options(fn () => Lookup::options(Lookup::TYPE_CUSTODY)),
                SelectFilter::make('status')->label('الحالة')->options(['delivered'=>'مسلَّمة','returned'=>'مُستردَّة','lost'=>'مفقودة','damaged'=>'تالفة']),
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
