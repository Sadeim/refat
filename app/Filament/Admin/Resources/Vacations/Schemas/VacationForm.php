<?php

namespace App\Filament\Admin\Resources\Vacations\Schemas;

use App\Models\Employee;
use App\Models\Vacation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VacationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الإجازة')->columns(2)->schema([
                Select::make('employee_id')->label('الموظف')->options(Employee::pluck('name_ar', 'id'))->searchable()->required(),
                Select::make('type')->label('نوع الإجازة')->options(Vacation::TYPES)->default('annual')->required(),
                DatePicker::make('start_date')->label('تاريخ البداية')->required()->native(false),
                DatePicker::make('end_date')->label('تاريخ النهاية')->required()->native(false)->afterOrEqual('start_date'),
                TextInput::make('days')->label('عدد الأيام (يُحسب تلقائياً)')->numeric()->disabled()->dehydrated(false),
                Select::make('status')->label('الحالة')->options(Vacation::STATUSES)->default('pending')->required(),
                Textarea::make('reason')->label('السبب')->columnSpanFull(),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]),
        ]);
    }
}
