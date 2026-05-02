<?php

namespace App\Filament\Admin\Resources\Custodies\Schemas;

use App\Models\Custody;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Lookup;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustodyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات العهدة')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_no')->label('رقم العهدة')->placeholder('سيُولَّد تلقائياً'),
                        Select::make('asset_type')->label('نوع المقتنى')
                            ->options(fn () => Lookup::options(Lookup::TYPE_CUSTODY))
                            ->required()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('label_ar')->label('الاسم بالعربي')->required(),
                                TextInput::make('key')->label('المفتاح (إنجليزي)')->required()->regex('/^[a-z0-9_]+$/'),
                            ])
                            ->createOptionUsing(fn (array $data) => tap($data['key'], fn () => Lookup::create([
                                'type' => Lookup::TYPE_CUSTODY,
                                'key' => $data['key'],
                                'label_ar' => $data['label_ar'],
                                'is_active' => true,
                            ]))),
                        TextInput::make('asset_name')->label('اسم المقتنى / الوصف')->required(),
                        TextInput::make('serial_no')->label('الرقم التسلسلي'),
                        TextInput::make('value')->label('القيمة')->numeric()->prefix('₪')->default(0),
                        Select::make('status')->label('الحالة')
                            ->options([
                                'delivered' => 'مسلَّمة',
                                'returned' => 'مُستردَّة',
                                'lost' => 'مفقودة',
                                'damaged' => 'تالفة',
                            ])->default('delivered')->required(),
                    ]),

                Section::make('المسلَّمة إليه')
                    ->columns(2)
                    ->schema([
                        Select::make('assigned_to_type')->label('النوع')->options([
                            'employee' => 'موظف',
                            'customer' => 'عميل',
                        ])->required()->live(),
                        Select::make('assigned_to_id')->label('الاسم')
                            ->options(fn (callable $get) => match ($get('assigned_to_type')) {
                                'employee' => Employee::pluck('name_ar', 'id'),
                                'customer' => Customer::pluck('name_ar', 'id'),
                                default => [],
                            })
                            ->searchable()
                            ->required(),
                    ]),

                Section::make('تواريخ التسليم/الاسترجاع')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('delivered_at')->label('تاريخ التسليم')->native(false)->default(now()),
                        DatePicker::make('returned_at')->label('تاريخ الاسترجاع')->native(false),
                        TextInput::make('condition_on_delivery')->label('الحالة عند التسليم'),
                        TextInput::make('condition_on_return')->label('الحالة عند الاسترجاع'),
                    ]),

                Section::make('الصور والملاحظات')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photos')->label('صور')->collection('photos')->multiple()->image(),
                        SpatieMediaLibraryFileUpload::make('documents')->label('وثائق')->collection('documents')->multiple()->downloadable(),
                        Textarea::make('notes')->label('ملاحظات'),
                    ]),
            ])
            ->columns(2);
    }
}
