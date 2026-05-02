<?php

namespace App\Filament\Admin\Resources\IncomingLetters\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncomingLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات البريد الوارد')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_no')->label('الرقم المرجعي')->placeholder('سيُولَّد تلقائياً'),
                        DatePicker::make('letter_date')->label('تاريخ الكتاب')->required()->native(false)->default(now()),
                        DatePicker::make('received_at')->label('تاريخ الاستلام')->native(false)->default(now()),
                        TextInput::make('from_party')->label('جهة الإرسال')->required(),
                        TextInput::make('subject')->label('الموضوع')->required()->columnSpanFull(),
                        Select::make('priority')->label('الأولوية')
                            ->options(['low' => 'منخفضة', 'normal' => 'عادية', 'high' => 'عالية', 'urgent' => 'عاجل'])
                            ->default('normal')->required(),
                        Select::make('status')->label('الحالة')
                            ->options(['open' => 'مفتوح', 'in_progress' => 'قيد المعالجة', 'closed' => 'مغلق', 'archived' => 'مؤرشف'])
                            ->default('open')->required(),
                        Select::make('assigned_to')->label('المسؤول')
                            ->options(User::pluck('name', 'id'))
                            ->searchable(),
                        Textarea::make('body')->label('نص الكتاب')->rows(5)->columnSpanFull(),
                    ]),
                Section::make('المرفقات')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('الملفات والصور')
                            ->collection('attachments')
                            ->multiple()
                            ->reorderable()
                            ->downloadable()
                            ->openable(),
                        Textarea::make('notes')->label('ملاحظات'),
                    ]),
            ])
            ->columns(2);
    }
}
