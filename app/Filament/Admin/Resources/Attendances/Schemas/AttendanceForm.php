<?php

namespace App\Filament\Admin\Resources\Attendances\Schemas;

use App\Models\Attendance;
use App\Models\Employee;
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
            Section::make('بيانات الحضور')->columns(2)->schema([
                Select::make('employee_id')->label('الموظف')->options(Employee::pluck('name_ar', 'id'))->searchable()->required(),
                DatePicker::make('date')->label('التاريخ')->required()->native(false)->default(now()),
                TimePicker::make('check_in')->label('وقت الدخول')->seconds(false),
                TimePicker::make('check_out')->label('وقت الخروج')->seconds(false),
                TextInput::make('hours')->label('عدد الساعات')->numeric()->default(0)->step(0.25),
                Select::make('status')->label('الحالة')->options(Attendance::STATUSES)->default('present')->required(),
                Select::make('check_in_method')->label('طريقة التسجيل')->options([
                    'manual' => 'يدوي', 'qr' => 'QR Code', 'fingerprint' => 'بصمة',
                ])->default('manual'),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]),
        ]);
    }
}
