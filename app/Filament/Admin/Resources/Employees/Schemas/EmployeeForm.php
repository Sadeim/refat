<?php

namespace App\Filament\Admin\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('البيانات الأساسية')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('الكود الوظيفي')
                            ->placeholder('سيُولَّد تلقائياً')
                            ->maxLength(50),
                        TextInput::make('name_ar')
                            ->label('الاسم بالعربي')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('name_en')
                            ->label('الاسم بالإنجليزي')
                            ->maxLength(255),
                        TextInput::make('national_id')
                            ->label('الرقم الوطني')
                            ->maxLength(50),
                        TextInput::make('phone')
                            ->label('الهاتف')
                            ->tel(),
                        TextInput::make('email')
                            ->label('البريد الإلكتروني')
                            ->email(),
                        DatePicker::make('dob')
                            ->label('تاريخ الميلاد')
                            ->native(false),
                        Textarea::make('address')
                            ->label('العنوان')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->label('الصورة الشخصية')
                            ->collection('photo')
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->columnSpanFull(),
                    ]),

                Section::make('بيانات الوظيفة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('position')
                            ->label('المسمى الوظيفي'),
                        TextInput::make('department')
                            ->label('القسم'),
                        Select::make('work_location')
                            ->label('مكان العمل')
                            ->options(fn () => \App\Models\Lookup::options('work_location'))
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                                TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                            ])
                            ->createOptionUsing(fn (array $data) => tap($data['key'], fn () => \App\Models\Lookup::create([
                                'type' => 'work_location', 'key' => $data['key'], 'label_ar' => $data['label_ar'], 'is_active' => true,
                            ]))),
                        Select::make('job_nature')
                            ->label('طبيعة العمل')
                            ->options(fn () => \App\Models\Lookup::options('job_nature'))
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                                TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                            ])
                            ->createOptionUsing(fn (array $data) => tap($data['key'], fn () => \App\Models\Lookup::create([
                                'type' => 'job_nature', 'key' => $data['key'], 'label_ar' => $data['label_ar'], 'is_active' => true,
                            ]))),
                        DatePicker::make('start_date')
                            ->label('تاريخ بدء العمل')
                            ->native(false),
                        DatePicker::make('end_date')
                            ->label('تاريخ انتهاء العمل')
                            ->native(false),
                        TimePicker::make('shift_start')
                            ->label('بداية الدوام')
                            ->seconds(false),
                        TimePicker::make('shift_end')
                            ->label('نهاية الدوام')
                            ->seconds(false),
                        TextInput::make('daily_hours')
                            ->label('ساعات العمل اليومية')
                            ->numeric()
                            ->default(8)
                            ->required(),
                        Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'on_leave' => 'في إجازة',
                                'suspended' => 'موقوف',
                                'terminated' => 'منتهي الخدمة',
                            ])
                            ->default('active')
                            ->required(),
                        Textarea::make('specs')
                            ->label('المواصفات والمؤهلات')
                            ->columnSpanFull()
                            ->helperText('وصف للمؤهلات والمهارات والتدريبات'),
                        Textarea::make('schedule')
                            ->label('جدول الدوام')
                            ->columnSpanFull()
                            ->helperText('تفاصيل أيام العمل وأي ترتيبات خاصة'),
                    ]),

                Section::make('البيانات المالية')
                    ->columns(2)
                    ->schema([
                        TextInput::make('basic_salary')
                            ->label('الراتب الأساسي')
                            ->numeric()
                            ->prefix('₪')
                            ->default(0),
                        TextInput::make('allowances')
                            ->label('البدلات')
                            ->numeric()
                            ->prefix('₪')
                            ->default(0),
                        TextInput::make('hourly_rate')
                            ->label('سعر ساعة العمل')
                            ->numeric()
                            ->prefix('₪')
                            ->default(0)
                            ->helperText('إذا الموظف بأجر ساعة، أدخل السعر هنا'),
                    ]),

                Section::make('المرفقات والملاحظات')
                    ->columns(1)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('documents')
                            ->label('الوثائق والمرفقات')
                            ->collection('documents')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable(),
                        Textarea::make('notes')
                            ->label('ملاحظات')
                            ->rows(3),
                    ]),
            ])
            ->columns(2);
    }
}
