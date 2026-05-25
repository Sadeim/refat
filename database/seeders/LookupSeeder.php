<?php

namespace Database\Seeders;

use App\Models\Lookup;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            Lookup::TYPE_CUSTOMER => [
                ['key' => 'individual',   'label_ar' => 'فرد'],
                ['key' => 'company',      'label_ar' => 'شركة'],
                ['key' => 'government',   'label_ar' => 'حكومي'],
                ['key' => 'embassy',      'label_ar' => 'سفارة'],
                ['key' => 'organization', 'label_ar' => 'منظمة'],
            ],

            Lookup::TYPE_CUSTODY => [
                ['key' => 'weapon',    'label_ar' => 'سلاح'],
                ['key' => 'vehicle',   'label_ar' => 'سيارة'],
                ['key' => 'radio',     'label_ar' => 'جهاز اتصال'],
                ['key' => 'uniform',   'label_ar' => 'زي رسمي'],
                ['key' => 'equipment', 'label_ar' => 'معدات'],
                ['key' => 'phone',     'label_ar' => 'هاتف'],
                ['key' => 'computer',  'label_ar' => 'كمبيوتر/لابتوب'],
                ['key' => 'other',     'label_ar' => 'أخرى'],
            ],

            Lookup::TYPE_EXPENSE_CAT => [
                ['key' => 'salary',       'label_ar' => 'رواتب'],
                ['key' => 'rent',         'label_ar' => 'إيجار'],
                ['key' => 'utilities',    'label_ar' => 'فواتير ومرافق'],
                ['key' => 'fuel',         'label_ar' => 'وقود'],
                ['key' => 'maintenance',  'label_ar' => 'صيانة'],
                ['key' => 'transport',    'label_ar' => 'مواصلات ونقل'],
                ['key' => 'communication','label_ar' => 'اتصالات وإنترنت'],
                ['key' => 'office',       'label_ar' => 'لوازم مكتبية'],
                ['key' => 'taxes',        'label_ar' => 'ضرائب ورسوم'],
                ['key' => 'insurance',    'label_ar' => 'تأمينات'],
                ['key' => 'training',     'label_ar' => 'تدريب وتطوير'],
                ['key' => 'marketing',    'label_ar' => 'تسويق وإعلانات'],
                ['key' => 'other',        'label_ar' => 'أخرى'],
            ],

            Lookup::TYPE_INCOME_CAT => [
                ['key' => 'service_revenue',   'label_ar' => 'إيراد خدمات'],
                ['key' => 'security_contract', 'label_ar' => 'عقود حماية'],
                ['key' => 'logistics',         'label_ar' => 'لوجستيات'],
                ['key' => 'consultation',      'label_ar' => 'استشارات'],
                ['key' => 'rent_income',       'label_ar' => 'إيجار مستلم'],
                ['key' => 'other',             'label_ar' => 'أخرى'],
            ],

            Lookup::TYPE_INVOICE_CAT => [
                ['key' => 'monthly_security', 'label_ar' => 'حماية شهرية'],
                ['key' => 'event_security',   'label_ar' => 'تأمين حدث/فعالية'],
                ['key' => 'vip_protection',   'label_ar' => 'حماية شخصيات'],
                ['key' => 'transport',        'label_ar' => 'نقل وحراسة شحنات'],
                ['key' => 'consultation',     'label_ar' => 'استشارات أمنية'],
                ['key' => 'training',         'label_ar' => 'تدريب أمني'],
                ['key' => 'equipment_rent',   'label_ar' => 'تأجير معدات'],
                ['key' => 'other',            'label_ar' => 'أخرى'],
            ],

            'work_location' => [
                ['key' => 'mall_aljundi',     'label_ar' => 'مول الجندي'],
                ['key' => 'office',           'label_ar' => 'المكتب الرئيسي'],
                ['key' => 'other',            'label_ar' => 'أخرى'],
            ],

            'job_nature' => [
                ['key' => 'security',         'label_ar' => 'أمن'],
                ['key' => 'cleaning',         'label_ar' => 'نظافة'],
                ['key' => 'admin',            'label_ar' => 'إداري'],
                ['key' => 'maintenance',      'label_ar' => 'صيانة'],
                ['key' => 'driver',           'label_ar' => 'سائق'],
                ['key' => 'supervisor',       'label_ar' => 'مشرف'],
                ['key' => 'other',            'label_ar' => 'أخرى'],
            ],
        ];

        foreach ($data as $type => $items) {
            $sort = 0;
            foreach ($items as $item) {
                Lookup::firstOrCreate(
                    ['type' => $type, 'key' => $item['key']],
                    array_merge($item, ['sort' => $sort++, 'is_active' => true])
                );
            }
        }
    }
}
