<?php

namespace App\Filament\Admin\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class Settings extends Page
{
    protected string $view = 'filament.admin.pages.settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'النظام';

    protected static ?int $navigationSort = 120;

    public ?array $data = [];

    public static function getNavigationLabel(): string { return 'إعدادات الشركة'; }
    public function getTitle(): string { return 'إعدادات الشركة'; }

    public function mount(): void
    {
        $this->form->fill([
            'company_name' => Setting::get('company_name', 'Trust Guard'),
            'company_name_en' => Setting::get('company_name_en', 'Trust Guard'),
            'company_email' => Setting::get('company_email'),
            'company_phone' => Setting::get('company_phone'),
            'company_address' => Setting::get('company_address'),
            'company_website' => Setting::get('company_website'),
            'tax_id' => Setting::get('tax_id'),
            'currency' => Setting::get('currency', 'ILS'),
            'currency_symbol' => Setting::get('currency_symbol', '₪'),
            'invoice_prefix' => Setting::get('invoice_prefix', 'INV'),
            'invoice_due_days' => Setting::get('invoice_due_days', 30),
            'invoice_tax_rate' => Setting::get('invoice_tax_rate', 17),
            'invoice_footer' => Setting::get('invoice_footer'),
            'logo_path' => Setting::get('logo_path'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make('بيانات الشركة')->columns(2)->schema([
                    TextInput::make('company_name')->label('اسم الشركة (عربي)')->required(),
                    TextInput::make('company_name_en')->label('اسم الشركة (إنجليزي)'),
                    TextInput::make('company_email')->label('البريد الإلكتروني')->email(),
                    TextInput::make('company_phone')->label('الهاتف')->tel(),
                    TextInput::make('company_website')->label('الموقع الإلكتروني')->url(),
                    TextInput::make('tax_id')->label('الرقم الضريبي'),
                    Textarea::make('company_address')->label('العنوان')->columnSpanFull(),
                    FileUpload::make('logo_path')->label('شعار الشركة')->image()->disk('public')->directory('settings')->avatar(),
                ]),

                Section::make('العملة والفواتير')->columns(2)->schema([
                    Select::make('currency')->label('العملة')->options([
                        'ILS' => '₪ شيكل (ILS)',
                        'USD' => '$ دولار (USD)',
                        'EUR' => '€ يورو (EUR)',
                        'JOD' => 'د.أ دينار أردني (JOD)',
                        'SAR' => 'ر.س ريال سعودي (SAR)',
                    ])->required(),
                    TextInput::make('currency_symbol')->label('رمز العملة')->maxLength(10),
                    TextInput::make('invoice_prefix')->label('بادئة رقم الفاتورة')->default('INV'),
                    TextInput::make('invoice_due_days')->label('أجل السداد (أيام)')->numeric()->default(30),
                    TextInput::make('invoice_tax_rate')->label('نسبة الضريبة %')->numeric()->default(17),
                    Textarea::make('invoice_footer')->label('تذييل الفاتورة')->rows(2)->columnSpanFull(),
                ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::set($key, is_scalar($value) ? (string) $value : json_encode($value));
        }

        Cache::flush();

        \Filament\Notifications\Notification::make()
            ->title('تم حفظ الإعدادات')
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('💾 حفظ الإعدادات')->action('save')->color('primary')];
    }
}
