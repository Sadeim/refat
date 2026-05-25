<?php

namespace App\Filament\Admin\Resources\Vehicles\Schemas;

use App\Models\Employee;
use App\Models\Vehicle;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات المركبة')->columns(3)->schema([
                TextInput::make('plate_number')->label('رقم اللوحة')->required()->unique(ignoreRecord: true),
                TextInput::make('make')->label('الشركة المصنعة')->placeholder('هيونداي / تويوتا...'),
                TextInput::make('model')->label('الموديل')->placeholder('سنتافيه / كامري...'),
                TextInput::make('year')->label('سنة الصنع')->numeric()->minValue(1980)->maxValue(2100),
                TextInput::make('color')->label('اللون'),
                TextInput::make('vin')->label('رقم الشاسيه (VIN)'),
                TextInput::make('current_odometer')->label('العداد الحالي (كم)')->numeric()->step(0.01)->default(0),
                Select::make('default_driver_id')->label('السائق الافتراضي')
                    ->options(Employee::pluck('name_ar', 'id'))->searchable(),
                Select::make('status')->label('الحالة')
                    ->options(Vehicle::STATUSES)->default('active')->required(),
            ]),

            Section::make('التراخيص والتأمين')->columns(2)->schema([
                DatePicker::make('license_expiry')->label('انتهاء الترخيص')->native(false),
                DatePicker::make('insurance_expiry')->label('انتهاء التأمين')->native(false),
            ]),

            Section::make('الصور والملاحظات')->columns(1)->schema([
                SpatieMediaLibraryFileUpload::make('photos')->label('صور المركبة')
                    ->collection('photos')->multiple()->image()->reorderable(),
                SpatieMediaLibraryFileUpload::make('documents')->label('وثائق (رخصة، تأمين، فحص...)')
                    ->collection('documents')->multiple()->downloadable()->openable(),
                Textarea::make('notes')->label('ملاحظات')->rows(3),
            ]),
        ]);
    }
}
