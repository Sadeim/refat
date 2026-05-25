<?php

namespace App\Filament\Admin\Resources\VehicleTrips\Schemas;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleTripForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('السيارة والسائق')->columns(3)->schema([
                Select::make('vehicle_id')->label('المركبة')
                    ->options(Vehicle::query()->get()->mapWithKeys(fn ($v) => [$v->id => trim($v->plate_number.' — '.$v->model)]))
                    ->searchable()->required()->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state && ($v = Vehicle::find($state))) {
                            $set('odometer_start', $v->current_odometer);
                            if ($v->default_driver_id) {
                                $set('driver_id', $v->default_driver_id);
                            }
                        }
                    }),
                Select::make('driver_id')->label('السائق (موظف)')
                    ->options(Employee::pluck('name_ar', 'id'))->searchable(),
                TextInput::make('driver_name')->label('اسم السائق (نص حر)')
                    ->placeholder('استخدم إذا السائق ليس موظفاً مسجَّلاً'),
            ]),

            Section::make('التاريخ والوقت')->columns(3)->schema([
                DatePicker::make('trip_date')->label('التاريخ')->required()->native(false)->default(now()),
                TimePicker::make('start_time')->label('وقت الحركة')->seconds(false),
                TimePicker::make('end_time')->label('وقت الانتهاء')->seconds(false),
            ]),

            Section::make('العداد والمسافة')->columns(3)->schema([
                TextInput::make('odometer_start')->label('العداد بداية (كم)')
                    ->numeric()->step(0.01)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $end = (float) ($get('odometer_end') ?? 0);
                        if ($end > 0) {
                            $set('distance_km', max(0, round($end - (float) $state, 2)));
                        }
                    }),
                TextInput::make('odometer_end')->label('العداد نهاية (كم)')
                    ->numeric()->step(0.01)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $start = (float) ($get('odometer_start') ?? 0);
                        $set('distance_km', max(0, round((float) $state - $start, 2)));
                    }),
                TextInput::make('distance_km')->label('المسافة (كم)')
                    ->numeric()->default(0)->disabled()->dehydrated(),
            ]),

            Section::make('المهمة')->columns(2)->schema([
                Select::make('customer_id')->label('العميل (إن وُجد)')
                    ->options(Customer::pluck('name_ar', 'id'))->searchable(),
                TextInput::make('destination')->label('الوجهة'),
                Textarea::make('purpose')->label('طبيعة المهمة')->rows(2)->columnSpanFull(),
            ]),

            Section::make('الوقود وملاحظات')->columns(2)->schema([
                TextInput::make('fuel_liters')->label('وقود (لتر)')->numeric()->step(0.01),
                TextInput::make('fuel_cost')->label('تكلفة الوقود')->numeric()->prefix('₪')->step(0.01),
                Textarea::make('notes')->label('ملاحظات')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }
}
