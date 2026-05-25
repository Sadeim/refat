<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Lookup;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الموظف والتاريخ')->columns(2)->schema([
                Select::make('employee_id')->label('الموظف')
                    ->options(Employee::pluck('name_ar', 'id'))
                    ->searchable()->required()->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state && ($emp = Employee::find($state))) {
                            $set('hourly_rate', $emp->hourly_rate ?? 0);
                            $set('work_location', $emp->work_location);
                        }
                    }),
                DatePicker::make('date')->label('التاريخ')->required()->native(false)->default(now()),
                Select::make('period')->label('الفترة')
                    ->options(Attendance::PERIODS)
                    ->default('morning'),
                Select::make('status')->label('حالة الدوام')
                    ->options(Attendance::STATUSES)
                    ->default('present')
                    ->required()
                    ->live(),
            ]),

            Section::make('الساعات والمبالغ')->columns(3)->schema([
                TimePicker::make('check_in')->label('وقت الدخول')->seconds(false)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        self::recalc($get, $set);
                    }),
                TimePicker::make('check_out')->label('وقت الخروج')->seconds(false)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        self::recalc($get, $set);
                    }),
                TextInput::make('hours')->label('عدد ساعات الدوام')
                    ->numeric()->step(0.25)->default(0)
                    ->suffix('ساعة')
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $rate = (float) ($get('hourly_rate') ?? 0);
                        $set('daily_total', round(((float) $state) * $rate, 2));
                    }),
                TextInput::make('hourly_rate')->label('سعر الساعة')
                    ->numeric()->prefix('₪')->default(0)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $hrs = (float) ($get('hours') ?? 0);
                        $set('daily_total', round($hrs * ((float) $state), 2));
                    }),
                TextInput::make('daily_total')->label('الإجمالي اليومي')
                    ->numeric()->prefix('₪')->default(0)
                    ->disabled()->dehydrated(),
                Select::make('work_location')->label('مكان العمل')
                    ->options(fn () => Lookup::options('work_location'))
                    ->searchable(),
                Select::make('check_in_method')->label('طريقة التسجيل')
                    ->options(['manual'=>'يدوي', 'qr'=>'QR Code', 'fingerprint'=>'بصمة'])
                    ->default('manual'),
            ]),

            Section::make('ملاحظات وتوقيع المشرف')->columns(2)->schema([
                Textarea::make('supervisor_notes')->label('ملاحظات المشرف')->rows(2),
                Select::make('supervisor_id')->label('المشرف الموقّع')
                    ->options(User::pluck('name', 'id'))
                    ->searchable(),
                Textarea::make('notes')->label('ملاحظات إضافية')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    /**
     * Recalculates `hours` from check_in/check_out, and updates `daily_total`.
     * Handles overnight shifts (when check_out < check_in, adds 24h).
     */
    protected static function recalc(callable $get, callable $set): void
    {
        $in  = $get('check_in');
        $out = $get('check_out');

        if (!$in || !$out) {
            return;
        }

        try {
            $start = \Carbon\Carbon::parse($in);
            $end   = \Carbon\Carbon::parse($out);

            // إذا الخروج في اليوم التالي (وردية ليلية)
            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            // حساب مباشر بالطوابع الزمنية — يضمن قيمة موجبة دائماً
            $seconds = $end->getTimestamp() - $start->getTimestamp();
            $hours = round(max(0, $seconds / 3600), 2);

            $set('hours', $hours);

            $rate = (float) ($get('hourly_rate') ?? 0);
            $set('daily_total', round($hours * $rate, 2));
        } catch (\Exception $e) {
            // تجاهل أي قيمة وقت غير صالحة
        }
    }
}
