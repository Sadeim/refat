<?php

namespace App\Filament\Admin\Resources\Customers\Tables;

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

class CustomersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('الكود')->searchable()->sortable(),
                TextColumn::make('name_ar')->label('الاسم')->searchable()->sortable(),
                BadgeColumn::make('type')->label('النوع')
                    ->formatStateUsing(fn (?string $state): string => Lookup::label(Lookup::TYPE_CUSTOMER, $state, $state)),
                TextColumn::make('phone')->label('الهاتف')->searchable(),
                TextColumn::make('contract_value')->label('قيمة العقد')->money('ILS')->sortable(),
                TextColumn::make('contract_end')->label('نهاية العقد')->date()->sortable(),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['success' => 'active', 'warning' => 'paused', 'danger' => 'expired'])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط', 'paused' => 'موقوف', 'expired' => 'منتهٍ', default => $state,
                    }),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('type')->label('النوع')
                    ->options(fn () => Lookup::options(Lookup::TYPE_CUSTOMER)),
                SelectFilter::make('status')->label('الحالة')->options([
                    'active' => 'نشط', 'paused' => 'موقوف', 'expired' => 'منتهٍ',
                ]),
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
