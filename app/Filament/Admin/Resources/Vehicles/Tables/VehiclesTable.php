<?php

namespace App\Filament\Admin\Resources\Vehicles\Tables;

use App\Models\Vehicle;
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

class VehiclesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->label('')
                    ->collection('photos')->circular(),
                TextColumn::make('plate_number')->label('رقم اللوحة')->searchable()->sortable()->weight('bold'),
                TextColumn::make('make')->label('الشركة')->searchable()->toggleable(),
                TextColumn::make('model')->label('الموديل')->searchable()->sortable(),
                TextColumn::make('year')->label('السنة')->sortable()->toggleable(),
                TextColumn::make('color')->label('اللون')->toggleable(),
                TextColumn::make('current_odometer')->label('العداد (كم)')->numeric()->sortable(),
                TextColumn::make('defaultDriver.name_ar')->label('السائق الافتراضي')->toggleable(),
                TextColumn::make('license_expiry')->label('انتهاء الترخيص')->date()->sortable()->toggleable()
                    ->color(fn ($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : ($state && \Carbon\Carbon::parse($state)->lessThan(now()->addMonth()) ? 'warning' : null)),
                TextColumn::make('insurance_expiry')->label('انتهاء التأمين')->date()->sortable()->toggleable()
                    ->color(fn ($state) => $state && \Carbon\Carbon::parse($state)->isPast() ? 'danger' : ($state && \Carbon\Carbon::parse($state)->lessThan(now()->addMonth()) ? 'warning' : null)),
                BadgeColumn::make('status')->label('الحالة')
                    ->colors(['success' => 'active', 'warning' => 'maintenance', 'danger' => 'retired'])
                    ->formatStateUsing(fn (string $state) => Vehicle::STATUSES[$state] ?? $state),
                TextColumn::make('trips_count')->label('عدد الرحلات')->counts('trips')->badge()->toggleable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(Vehicle::STATUSES),
                TrashedFilter::make()->label('المحذوفة'),
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
