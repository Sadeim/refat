<?php

namespace App\Filament\Admin\Resources\VehicleTrips\Tables;

use App\Models\Vehicle;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VehicleTripsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('trip_date')->label('التاريخ')->date()->sortable(),
                TextColumn::make('vehicle.plate_number')->label('اللوحة')->searchable()->sortable()->weight('bold'),
                TextColumn::make('vehicle.model')->label('الموديل')->toggleable(),
                TextColumn::make('driver_name')->label('السائق')->searchable(),
                TextColumn::make('start_time')->label('من')->time('H:i'),
                TextColumn::make('end_time')->label('إلى')->time('H:i'),
                TextColumn::make('odometer_start')->label('عداد بداية')->numeric()->toggleable(),
                TextColumn::make('odometer_end')->label('عداد نهاية')->numeric()->toggleable(),
                TextColumn::make('distance_km')->label('المسافة (كم)')->numeric()->weight('bold')->color('primary')->sortable(),
                TextColumn::make('purpose')->label('طبيعة المهمة')->limit(40)->wrap()->toggleable(),
                TextColumn::make('customer.name_ar')->label('العميل')->toggleable(),
                TextColumn::make('fuel_cost')->label('وقود')->money('ILS')->toggleable(),
            ])
            ->defaultSort('trip_date', 'desc')
            ->filters([
                SelectFilter::make('vehicle_id')->label('المركبة')
                    ->options(fn () => Vehicle::query()->get()->mapWithKeys(fn ($v) => [$v->id => trim($v->plate_number.' — '.$v->model)])),
                Filter::make('date_range')
                    ->label('فترة')
                    ->schema([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(fn ($q, array $data) => $q
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('trip_date', '>=', $d))
                        ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('trip_date', '<=', $d))),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
