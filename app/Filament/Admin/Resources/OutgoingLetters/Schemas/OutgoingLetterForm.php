<?php

namespace App\Filament\Admin\Resources\OutgoingLetters\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OutgoingLetterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('بيانات البريد الصادر')
                    ->columns(2)
                    ->schema([
                        TextInput::make('reference_no')->label('الرقم المرجعي')->placeholder('سيُولَّد تلقائياً'),
                        DatePicker::make('letter_date')->label('تاريخ الكتاب')->required()->native(false)->default(now()),
                        DatePicker::make('sent_at')->label('تاريخ الإرسال')->native(false),
                        TextInput::make('to_party')->label('الجهة المرسل إليها')->required(),
                        TextInput::make('subject')->label('الموضوع')->required()->columnSpanFull(),
                        Select::make('priority')->label('الأولوية')
                            ->options(['low' => 'منخفضة', 'normal' => 'عادية', 'high' => 'عالية', 'urgent' => 'عاجل'])
                            ->default('normal')->required(),
                        Select::make('status')->label('الحالة')
                            ->options(['draft' => 'مسودة', 'sent' => 'مُرسل', 'delivered' => 'مُستلم', 'archived' => 'مؤرشف'])
                            ->default('draft')->required(),
                        Select::make('created_by')->label('بواسطة')
                            ->options(User::pluck('name', 'id'))
                            ->default(fn () => auth()->id())
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
