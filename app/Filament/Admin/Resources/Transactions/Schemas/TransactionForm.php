<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Lookup;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات الحركة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_no')->label('الرقم المرجعي')->placeholder('سيُولَّد تلقائياً'),
                        Select::make('type')->label('النوع')->options(Transaction::TYPES)->required()->live(),
                        Select::make('category')->label('التصنيف')
                            ->options(fn (callable $get) => $get('type') === 'income'
                                ? Lookup::options(Lookup::TYPE_INCOME_CAT)
                                : Lookup::options(Lookup::TYPE_EXPENSE_CAT))
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                                TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                            ])
                            ->createOptionUsing(function (array $data, callable $get) {
                                $type = $get('type') === 'income' ? Lookup::TYPE_INCOME_CAT : Lookup::TYPE_EXPENSE_CAT;
                                Lookup::create([
                                    'type' => $type,
                                    'key' => $data['key'],
                                    'label_ar' => $data['label_ar'],
                                    'is_active' => true,
                                ]);
                                return $data['key'];
                            }),
                        TextInput::make('amount')->label('المبلغ')->numeric()->prefix('₪')->required(),
                        TextInput::make('currency')->label('العملة')->default('ILS')->required(),
                        DatePicker::make('transaction_date')->label('تاريخ الحركة')->required()->native(false)->default(now()),
                        Select::make('payment_method')->label('وسيلة الدفع')->options([
                            'cash' => 'نقداً', 'bank' => 'تحويل بنكي', 'cheque' => 'شيك', 'card' => 'بطاقة',
                        ]),
                        Select::make('status')->label('الحالة')->options([
                            'pending' => 'قيد التأكيد', 'confirmed' => 'مؤكدة', 'cancelled' => 'ملغاة',
                        ])->default('confirmed')->required(),
                    ]),

                Section::make('الجهة المعنية')
                    ->columns(2)
                    ->schema([
                        Select::make('party_type')->label('نوع الجهة')->options([
                            'employee' => 'موظف', 'customer' => 'عميل',
                        ])->live(),
                        Select::make('party_id')->label('الاسم')
                            ->options(fn (callable $get) => match ($get('party_type')) {
                                'employee' => Employee::pluck('name_ar', 'id'),
                                'customer' => Customer::pluck('name_ar', 'id'),
                                default => [],
                            })
                            ->searchable(),
                        TextInput::make('description')->label('الوصف')->columnSpanFull(),
                    ]),

                Section::make('المرفقات')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('attachments')->label('الإيصالات/الملفات')->collection('attachments')->multiple()->downloadable()->openable(),
                    ]),
            ])
            ->columns(1);
    }
}
