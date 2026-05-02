<?php

namespace App\Filament\Admin\Resources\Invoices\Schemas;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Lookup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الفاتورة')->columns(3)->schema([
                TextInput::make('number')->label('رقم الفاتورة')->placeholder('سيُولَّد تلقائياً'),
                Select::make('customer_id')->label('العميل')->options(Customer::pluck('name_ar', 'id'))->searchable()->required(),
                Select::make('status')->label('الحالة')->options(Invoice::STATUSES)->default('draft')->required(),
                DatePicker::make('issue_date')->label('تاريخ الإصدار')->native(false)->default(now())->required(),
                DatePicker::make('due_date')->label('تاريخ الاستحقاق')->native(false)->default(now()->addDays(30)),
                Select::make('category')->label('تصنيف الفاتورة')
                    ->options(fn () => Lookup::options(Lookup::TYPE_INVOICE_CAT))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                        TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                    ])
                    ->createOptionUsing(fn (array $data) => tap($data['key'], fn () => Lookup::create([
                        'type' => Lookup::TYPE_INVOICE_CAT,
                        'key' => $data['key'],
                        'label_ar' => $data['label_ar'],
                        'is_active' => true,
                    ]))),
                Textarea::make('statement')->label('بيان الفاتورة')
                    ->rows(2)->columnSpanFull()
                    ->placeholder('وصف موجز يُطبع على الفاتورة')
                    ->maxLength(500),
            ]),

            Section::make('البنود')->schema([
                Repeater::make('items')
                    ->relationship('items')
                    ->label('')
                    ->schema([
                        TextInput::make('description')->label('الوصف')->required()->columnSpan(3),
                        TextInput::make('quantity')->label('الكمية')->numeric()->default(1)->required(),
                        TextInput::make('unit_price')->label('سعر الوحدة')->numeric()->prefix('₪')->default(0)->required(),
                    ])
                    ->columns(5)
                    ->defaultItems(1)
                    ->reorderable()
                    ->cloneable()
                    ->collapsible(),
            ]),

            Section::make('الإجماليات والملاحظات')->columns(3)->schema([
                TextInput::make('tax')->label('ضريبة')->numeric()->prefix('₪')->default(0),
                TextInput::make('discount')->label('خصم')->numeric()->prefix('₪')->default(0),
                TextInput::make('total')->label('الإجمالي (يُحسب تلقائياً)')->numeric()->prefix('₪')->disabled()->dehydrated(false),
                Textarea::make('notes')->label('ملاحظات')->columnSpanFull(),
            ]),
        ]);
    }
}
