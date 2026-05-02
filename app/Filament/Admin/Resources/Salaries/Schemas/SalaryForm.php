<?php

namespace App\Filament\Admin\Resources\Salaries\Schemas;

use App\Models\Employee;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SalaryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الراتب')
                    ->columns(3)
                    ->schema([
                        Select::make('employee_id')->label('الموظف')
                            ->options(Employee::pluck('name_ar', 'id'))
                            ->searchable()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && ($emp = Employee::find($state))) {
                                    $set('basic', $emp->basic_salary);
                                    $set('allowances', $emp->allowances);
                                }
                            })
                            ->live(),
                        Select::make('year')->label('السنة')
                            ->options(collect(range(now()->year - 3, now()->year + 1))->mapWithKeys(fn ($y) => [$y => $y]))
                            ->default(now()->year)->required(),
                        Select::make('month')->label('الشهر')
                            ->options([1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'])
                            ->default(now()->month)->required(),
                    ]),

                Section::make('المبالغ')
                    ->columns(3)
                    ->schema([
                        TextInput::make('basic')->label('الراتب الأساسي')->numeric()->prefix('₪')->default(0),
                        TextInput::make('allowances')->label('البدلات')->numeric()->prefix('₪')->default(0),
                        TextInput::make('overtime')->label('عمل إضافي')->numeric()->prefix('₪')->default(0),
                        TextInput::make('advances')->label('سُلَف')->numeric()->prefix('₪')->default(0),
                        TextInput::make('deductions')->label('خصومات')->numeric()->prefix('₪')->default(0),
                        TextInput::make('net')->label('الصافي (يُحسب تلقائياً)')->numeric()->prefix('₪')->disabled()->dehydrated(false),
                    ]),

                Section::make('الحالة')
                    ->columns(2)
                    ->schema([
                        Select::make('status')->label('الحالة')
                            ->options(['draft'=>'مسودة','approved'=>'معتمد','paid'=>'مدفوع'])
                            ->default('draft')->required(),
                        DatePicker::make('paid_at')->label('تاريخ الصرف')->native(false),
                        Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }
}
