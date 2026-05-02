<?php

namespace App\Filament\Admin\Resources\Customers\Schemas;

use App\Models\Lookup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات العميل')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')->label('الكود')->placeholder('سيُولَّد تلقائياً'),
                        Select::make('type')->label('نوع العميل')
                            ->options(fn () => Lookup::options(Lookup::TYPE_CUSTOMER))
                            ->default('individual')
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                                TextInput::make('key')->label('المفتاح (إنجليزي بدون مسافات)')
                                    ->required()->regex('/^[a-z0-9_]+$/'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                Lookup::create([
                                    'type' => Lookup::TYPE_CUSTOMER,
                                    'key' => $data['key'],
                                    'label_ar' => $data['label_ar'],
                                    'is_active' => true,
                                ]);
                                return $data['key'];
                            }),
                        TextInput::make('name_ar')->label('الاسم بالعربي')->required(),
                        TextInput::make('name_en')->label('الاسم بالإنجليزي'),
                        TextInput::make('phone')->label('الهاتف')->tel(),
                        TextInput::make('email')->label('البريد الإلكتروني')->email(),
                        Textarea::make('address')->label('العنوان')->columnSpanFull(),
                        TextInput::make('contact_person')->label('شخص الاتصال'),
                        TextInput::make('contact_phone')->label('هاتف الاتصال')->tel(),
                        TextInput::make('tax_id')->label('الرقم الضريبي / السجل'),
                        SpatieMediaLibraryFileUpload::make('logo')->label('شعار/صورة')->collection('logo')->image()->avatar(),
                    ]),

                Section::make('العقد')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('contract_start')->label('بداية العقد')->native(false),
                        DatePicker::make('contract_end')->label('نهاية العقد')->native(false),
                        TextInput::make('contract_value')->label('قيمة العقد')->numeric()->prefix('₪')->default(0),
                        Select::make('status')->label('الحالة')
                            ->options([
                                'active' => 'نشط',
                                'paused' => 'موقوف',
                                'expired' => 'منتهٍ',
                            ])->default('active')->required(),
                    ]),

                Section::make('المرفقات والملاحظات')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('documents')->label('الوثائق')->collection('documents')->multiple()->reorderable()->downloadable()->openable(),
                        Textarea::make('notes')->label('ملاحظات')->rows(3),
                    ]),
            ])
            ->columns(2);
    }
}
