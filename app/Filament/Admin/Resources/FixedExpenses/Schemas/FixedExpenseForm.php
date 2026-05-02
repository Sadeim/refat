<?php

namespace App\Filament\Admin\Resources\FixedExpenses\Schemas;

use App\Models\FixedExpense;
use App\Models\Lookup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FixedExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات المصروف الثابت')->columns(2)->schema([
                    TextInput::make('name')->label('اسم المصروف')->required()
                        ->placeholder('مثال: إيجار المكتب، إنترنت، فاتورة كهرباء...'),
                    Select::make('category')->label('التصنيف')
                        ->options(fn () => Lookup::options(Lookup::TYPE_EXPENSE_CAT))
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                            TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                        ])
                        ->createOptionUsing(fn (array $data) => tap($data['key'], fn () => Lookup::create([
                            'type' => Lookup::TYPE_EXPENSE_CAT,
                            'key' => $data['key'],
                            'label_ar' => $data['label_ar'],
                            'is_active' => true,
                        ]))),
                    TextInput::make('amount')->label('المبلغ')->numeric()->prefix('₪')->required(),
                    TextInput::make('currency')->label('العملة')->default('ILS')->required(),
                    Select::make('frequency')->label('التكرار')
                        ->options(FixedExpense::FREQUENCIES)
                        ->default('monthly')
                        ->required()
                        ->live(),
                    TextInput::make('day_of_period')->label('اليوم من الشهر/الأسبوع')
                        ->numeric()->minValue(1)->maxValue(31)->default(1)
                        ->helperText('في الشهري: يوم الشهر (1-28). في الأسبوعي: يوم الأسبوع (1-7)'),
                    Select::make('payment_method')->label('وسيلة الدفع')->options([
                        'cash' => 'نقداً', 'bank' => 'تحويل بنكي', 'cheque' => 'شيك', 'card' => 'بطاقة',
                    ]),
                ]),

                Section::make('فترة السريان')->columns(2)->schema([
                    DatePicker::make('start_date')->label('تاريخ البداية')->native(false)->default(now())->required(),
                    DatePicker::make('end_date')->label('تاريخ الانتهاء (اختياري)')->native(false),
                    DatePicker::make('next_run_at')->label('تاريخ الاستحقاق القادم')->native(false)
                        ->helperText('سيُحسب تلقائياً عند الإنشاء إذا تركته فارغاً'),
                    DatePicker::make('last_run_at')->label('آخر تنفيذ')->native(false)->disabled(),
                ]),

                Section::make('الإعدادات')->columns(2)->schema([
                    Toggle::make('is_active')->label('نشط')->default(true),
                    Toggle::make('auto_post')->label('قيد تلقائي عند الاستحقاق')
                        ->helperText('إذا فعّلت، يُنشأ Transaction في تاريخ الاستحقاق دون تدخل')->default(false),
                    Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
                ]),
            ])
            ->columns(2);
    }
}
