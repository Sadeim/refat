<?php

namespace App\Filament\Admin\Resources\Vehicles\RelationManagers;

use App\Models\Customer;
use App\Models\Employee;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TripsRelationManager extends RelationManager
{
    protected static string $relationship = 'trips';

    protected static ?string $title = 'سجل حركات المركبة';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-arrow-trending-up';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الرحلة')->columns(3)->schema([
                DatePicker::make('trip_date')->label('التاريخ')->required()->native(false)->default(now()),
                Select::make('driver_id')->label('السائق (موظف)')
                    ->options(Employee::pluck('name_ar', 'id'))->searchable(),
                TextInput::make('driver_name')->label('اسم السائق (نص حر)')
                    ->placeholder('استخدم إذا السائق ليس موظفاً مسجَّلاً'),
                TimePicker::make('start_time')->label('وقت الحركة')->seconds(false),
                TimePicker::make('end_time')->label('وقت الانتهاء')->seconds(false),
                Select::make('customer_id')->label('العميل (إن وُجد)')
                    ->options(Customer::pluck('name_ar', 'id'))->searchable(),
            ]),

            Section::make('العداد والمسافة')->columns(3)->schema([
                TextInput::make('odometer_start')->label('العداد بداية (كم)')
                    ->numeric()->step(0.01)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $end = (float) ($get('odometer_end') ?? 0);
                        if ($end > 0) {
                            $set('distance_km', max(0, round($end - (float) $state, 2)));
                        }
                    }),
                TextInput::make('odometer_end')->label('العداد نهاية (كم)')
                    ->numeric()->step(0.01)
                    ->live(debounce: 400)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $start = (float) ($get('odometer_start') ?? 0);
                        $set('distance_km', max(0, round((float) $state - $start, 2)));
                    }),
                TextInput::make('distance_km')->label('المسافة (كم)')
                    ->numeric()->step(0.01)->default(0)
                    ->disabled()->dehydrated(),
            ]),

            Section::make('الوقود (اختياري)')->columns(2)->schema([
                TextInput::make('fuel_liters')->label('وقود (لتر)')->numeric()->step(0.01),
                TextInput::make('fuel_cost')->label('تكلفة الوقود')->numeric()->prefix('₪')->step(0.01),
            ]),

            Section::make('المهمة')->columns(1)->schema([
                TextInput::make('destination')->label('الوجهة'),
                Textarea::make('purpose')->label('طبيعة المهمة')->rows(2),
                Textarea::make('notes')->label('ملاحظات')->rows(2),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('trip_date')->label('التاريخ')->date()->sortable(),
                TextColumn::make('start_time')->label('من')->time('H:i'),
                TextColumn::make('end_time')->label('إلى')->time('H:i'),
                TextColumn::make('driver_name')->label('السائق')->searchable(),
                TextColumn::make('odometer_start')->label('عداد بداية')->numeric()->toggleable(),
                TextColumn::make('odometer_end')->label('عداد نهاية')->numeric()->toggleable(),
                TextColumn::make('distance_km')->label('المسافة (كم)')->numeric()->weight('bold')->color('primary'),
                TextColumn::make('purpose')->label('طبيعة المهمة')->limit(40)->wrap(),
                TextColumn::make('customer.name_ar')->label('العميل')->toggleable(),
                TextColumn::make('fuel_cost')->label('وقود')->money('ILS')->toggleable(),
            ])
            ->defaultSort('trip_date', 'desc')
            ->filters([
                Filter::make('date_range')
                    ->label('فترة')
                    ->schema([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(fn ($q, array $data) => $q
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('trip_date', '>=', $d))
                        ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('trip_date', '<=', $d))),
            ])
            ->headerActions([
                CreateAction::make()->label('+ إضافة رحلة'),
            ])
            ->recordActions([
                EditAction::make()->label('تعديل'),
                DeleteAction::make()->label('حذف'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
