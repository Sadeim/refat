# Trust Guard — نظام الإدارة المتكامل

> **الشركة**: Trust Guard (ترست جارد) — شركة عربية للحماية واللوجستيات
> **المشروع**: لوحة تحكم متكاملة لإدارة الموظفين، العملاء، المراسلات، العهد، المحاسبة، والتقارير
> **التقنية**: Laravel 12 + Filament 3 + SQLite/MySQL + Laravel Valet
> **اللغة**: عربي (RTL) + إنجليزي

---

## 1. النظرة العامة

نظام داشبورد إداري متكامل يخدم احتياجات شركة حماية ولوجستيات، يوفر:

- إدارة كاملة للموظفين (بطاقات تعريف بـ QR Code، صور، مواصفات، دوام، رواتب)
- إدارة العملاء (نوع العميل، نوع الخدمة، سيارات، أسلحة، تأمين شخصي)
- أرشيف المراسلات (وارد / صادر) مع المرفقات
- نظام عهد ومقتنيات وتسليم
- محاسبة كاملة (موظفين + مؤسسات)
- تقارير (يومية، أسبوعية، شهرية، فردية، عامة)
- تصدير Excel و Word
- نظام صلاحيات (Roles & Permissions)

---

## 2. الوحدات (Modules)

### 2.1. المستخدمون والصلاحيات
- Users + Roles + Permissions (Spatie)
- أدوار افتراضية: Super Admin, Admin, Accountant, HR, Archive Officer, Viewer

### 2.2. الموظفون (Employees)
- بيانات شخصية كاملة + صورة شخصية + QR Code
- مواصفات، تاريخ بدء العمل، أوقات الدوام
- ساعات العمل (Time Tracking)
- ربط بالرواتب والعهد

### 2.3. العملاء (Customers)
- بطاقة عمل كاملة
- نوع العميل (فرد / شركة / مؤسسة حكومية...)
- نوع الخدمة:
  - حماية سيارات
  - حماية أسلحة
  - تأمين شخصي (Personal Security)
- ملفات ووثائق

### 2.4. أرشيف المراسلات
- **الوارد** (Incoming): رقم، تاريخ، جهة الإرسال، الموضوع، مرفقات
- **الصادر** (Outgoing): رقم، تاريخ، الجهة المستهدفة، الموضوع، مرفقات
- أرقام تسلسلية تلقائية
- بحث متقدم

### 2.5. العهد والمقتنيات (Custody)
- تسجيل عهدة (سلاح، سيارة، معدات...)
- ربط بموظف أو عميل
- تاريخ التسليم وتاريخ الاسترجاع
- توقيع/استلام

### 2.6. المحاسبة (Accounting)
- رواتب الموظفين (شهرية + سلف + خصومات)
- الإيرادات من العملاء/المؤسسات
- المصروفات
- الأرصدة

### 2.7. التقارير (Reports)
- تقارير يومية / أسبوعية / شهرية
- تقارير فردية (لكل موظف)
- تقارير الشركة الكاملة
- تصدير Excel / Word / PDF

### 2.8. الملفات والصور
- رفع وحفظ الصور والملفات لكل وحدة
- معاينة وتحميل

---

## 3. خطة المراحل (Phases)

### ✅ Phase 0 — Bootstrap (مكتمل)
- إنشاء مشروع Laravel `refat` في مجلد Sites
- تشغيل migrations الافتراضية

### ✅ Phase 1 — Foundation (الأساس) — مكتمل
- تثبيت Filament 3 (Admin Panel)
- تثبيت Spatie Permissions
- إعداد اللغة العربية + RTL
- ربط Valet (`refat.test`)
- تكوين قاعدة البيانات (MySQL/SQLite)
- نموذج المستخدم الموسّع
- نظام تسجيل الدخول
- البنية الأساسية للداشبورد

### ✅ Phase 2 — Employees Module — مكتمل
- جدول الموظفين الكامل
- رفع الصور
- توليد QR Code لكل موظف
- بطاقة موظف للطباعة
- إدارة الدوام وساعات العمل
- إدارة المواصفات

### ✅ Phase 3 — Customers Module — مكتمل
- جدول العملاء
- أنواع العملاء + الخدمات
- جداول فرعية: السيارات، الأسلحة، التأمين الشخصي
- بطاقة عمل للعميل (للطباعة)
- مرفقات

### ✅ Phase 4 — Archive Module — مكتمل
- جدول الوارد + المرفقات
- جدول الصادر + المرفقات
- ترقيم تلقائي
- بحث وفلترة

### ✅ Phase 5 — Custody Module — مكتمل
- جدول العهد
- تسليم / استرجاع
- ربط بموظف/عميل

### ✅ Phase 6 — Accounting Module — مكتمل
- الرواتب
- السلف والخصومات
- الإيرادات والمصروفات
- ربط مع الموظفين والعملاء

### ✅ Phase 7 — Reports & Exports — مكتمل
- محرك تقارير قابل للفلترة
- تصدير Excel (Maatwebsite/Excel)
- تصدير Word (PhpOffice/PhpWord)
- تصدير PDF (DomPDF)

### ✅ Phase 8 — Polish & Deploy — مكتمل (Widgets جاهزة)
- ودجتس الداشبورد (Stats, Charts)
- إشعارات
- نسخ احتياطي
- إعدادات النظام
- اختبار شامل

---

## 4. الحزم (Packages)

| الحزمة | الاستخدام |
|--------|-----------|
| `filament/filament` | لوحة الإدارة |
| `spatie/laravel-permission` | الصلاحيات |
| `simplesoftwareio/simple-qrcode` | توليد QR |
| `maatwebsite/excel` | تصدير Excel |
| `phpoffice/phpword` | تصدير Word |
| `barryvdh/laravel-dompdf` | تصدير PDF |
| `spatie/laravel-medialibrary` | إدارة الصور والملفات |
| `spatie/laravel-activitylog` | سجل الأنشطة |

---

## 5. هيكل قاعدة البيانات (مبدئي)

```
users (Filament + Spatie)
roles, permissions, model_has_roles, ...

employees
  - id, code, name_ar, name_en, photo, qr_code
  - national_id, phone, address, dob
  - position, specs (json), start_date
  - daily_hours, schedule (json)
  - basic_salary, status

customers
  - id, code, name_ar, name_en, type (individual/company/gov)
  - phone, email, address, contact_person
  - notes

customer_services
  - id, customer_id, service_type (cars/weapons/personal_security)
  - details (json), start_date, end_date

incoming_letters
  - id, number, date, from_party, subject, body, status

outgoing_letters
  - id, number, date, to_party, subject, body, status

custodies
  - id, asset_name, asset_type, serial_no
  - assigned_to_type (employee/customer), assigned_to_id
  - delivered_at, returned_at, status

salaries
  - id, employee_id, month, year
  - basic, allowances, deductions, net

transactions  (الإيرادات/المصروفات)
  - id, type, amount, date, party_type, party_id
  - description, attachment

attachments / media (Spatie MediaLibrary)
  - متعدد الأشكال
```

---

## 6. ملاحظات النشر

- **Local**: Laravel Valet → `http://refat.test`
- **Production (لاحقاً)**: Hostinger أو VPS

---

## 7. الدخول الأولي

| العنوان | القيمة |
|---------|--------|
| الرابط | `http://refat.test/admin` (أو `http://127.0.0.1:8000/admin` مع `php artisan serve`) |
| البريد | `admin@trustguard.local` |
| كلمة السر | `password` |

> ⚠️ غيِّر كلمة السر فور الدخول الأول.

---

## 8. القائمة الجانبية (Navigation)

- **الموارد البشرية**: الموظفون
- **العملاء**: العملاء (+ الخدمات: سيارات/أسلحة/تأمين شخصي عبر RelationManager)
- **الأرشيف**: الوارد، الصادر
- **العهد والمقتنيات**: العهد
- **المحاسبة**: الرواتب، الإيرادات والمصروفات
- **التقارير**: التقارير والتصدير (Excel + Word)

---

## 9. روابط خاصة

- بطاقة موظف: `/employees/{id}/card` — قابلة للطباعة، تحتوي QR
- التحقق من QR: `/employees/verify/{token}` — صفحة التحقق العامة عند مسح الـ QR
- التقرير المالي Word: `/reports/financial-word`

---

## 10. الإضافات المتقدمة (Phase 9)

| # | الميزة | الحالة | الموقع |
|---|--------|--------|--------|
| 1 | إعدادات الشركة | ✅ | `/admin/settings` |
| 2 | إدارة المستخدمين | ✅ | `/admin/users` |
| 3 | إدارة الأدوار | ✅ | `/admin/roles` |
| 4 | سجل الأنشطة | ✅ | `/admin/activities` |
| 5 | بحث عام | ✅ | شريط البحث في Topbar |
| 6 | الإجازات | ✅ | `/admin/vacations` |
| 7 | الحضور والانصراف | ✅ | `/admin/attendances` + `/attendance/scan/{token}` |
| 8 | الفواتير والمدفوعات | ✅ | `/admin/invoices` |
| 9 | المهام | ✅ | `/admin/tasks` |
| 10 | استيراد Excel | ✅ | زر في صفحة الموظفين/العملاء |
| 11 | 2FA (Google Authenticator) | ✅ | `/admin/profile` |
| 12 | نسخ احتياطية يومية | ✅ | عبر `php artisan schedule:work` |
| 13-16 | الودجتس الذكية | ✅ | Dashboard |

### تشغيل النسخ الاحتياطي

أضف في cron:
```
* * * * * cd /path-to/refat && php artisan schedule:run >> /dev/null 2>&1
```

### تفعيل 2FA

- ادخل `/admin/profile` → فعّل المصادقة الثنائية
- امسح QR بتطبيق Google Authenticator أو Authy
- احفظ رموز الاستعادة

---

## 11. روابط QR الجديدة

- بطاقة موظف: `/employees/{id}/card`
- التحقق من QR الموظف: `/employees/verify/{token}`
- مسح QR للحضور: `/attendance/scan/{token}` — أول مسح يومياً = دخول، الثاني = خروج
- التقرير المالي Word: `/reports/financial-word`

---

_هذا الملف يُحدَّث مع تقدم المراحل._
