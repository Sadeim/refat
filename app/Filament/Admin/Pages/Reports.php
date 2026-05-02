<?php

namespace App\Filament\Admin\Pages;

use App\Exports\CustodiesExport;
use App\Exports\CustomersExport;
use App\Exports\EmployeesExport;
use App\Exports\LettersExport;
use App\Exports\SalariesExport;
use App\Exports\TransactionsExport;
use App\Models\Customer;
use App\Models\Custody;
use App\Models\Employee;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Salary;
use App\Models\Transaction;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class Reports extends Page
{
    protected string $view = 'filament.admin.pages.reports';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'التقارير';

    protected static ?int $navigationSort = 90;

    public static function getNavigationLabel(): string
    {
        return 'التقارير والتصدير';
    }

    public function getTitle(): string
    {
        return 'التقارير والتصدير';
    }

    public function getViewData(): array
    {
        $income = Transaction::where('type', 'income')->where('status', 'confirmed')->sum('amount');
        $expense = Transaction::where('type', 'expense')->where('status', 'confirmed')->sum('amount');
        $monthIncome = Transaction::where('type', 'income')->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->sum('amount');
        $monthExpense = Transaction::where('type', 'expense')->whereYear('transaction_date', now()->year)->whereMonth('transaction_date', now()->month)->sum('amount');
        $todayIncome = Transaction::where('type', 'income')->whereDate('transaction_date', today())->sum('amount');

        return [
            'stats' => [
                ['label' => 'إجمالي الإيرادات', 'value' => number_format($income, 2).' ₪', 'color' => 'emerald'],
                ['label' => 'إجمالي المصروفات', 'value' => number_format($expense, 2).' ₪', 'color' => 'red'],
                ['label' => 'الصافي', 'value' => number_format($income - $expense, 2).' ₪', 'color' => $income - $expense >= 0 ? 'blue' : 'red'],
                ['label' => 'إيرادات اليوم', 'value' => number_format($todayIncome, 2).' ₪', 'color' => 'gray'],
                ['label' => 'إيرادات الشهر', 'value' => number_format($monthIncome, 2).' ₪', 'color' => 'gray'],
                ['label' => 'مصروفات الشهر', 'value' => number_format($monthExpense, 2).' ₪', 'color' => 'gray'],
                ['label' => 'إجمالي الموظفين', 'value' => Employee::count().' (نشط: '.Employee::where('status', 'active')->count().')', 'color' => 'gray'],
                ['label' => 'إجمالي العملاء', 'value' => Customer::count().' (نشط: '.Customer::where('status', 'active')->count().')', 'color' => 'gray'],
                ['label' => 'العهد المسلَّمة', 'value' => Custody::where('status', 'delivered')->count(), 'color' => 'gray'],
                ['label' => 'الوارد / الصادر', 'value' => IncomingLetter::count().' / '.OutgoingLetter::count(), 'color' => 'gray'],
                ['label' => 'الرواتب المدفوعة', 'value' => number_format(Salary::where('status', 'paid')->sum('net'), 2).' ₪', 'color' => 'gray'],
                ['label' => 'الرواتب المعلقة', 'value' => Salary::whereIn('status', ['draft', 'approved'])->count(), 'color' => 'gray'],
            ],

            'reports' => [
                'الموارد البشرية' => [
                    ['title' => 'جميع الموظفين', 'desc' => 'قائمة شاملة بكل الموظفين', 'method' => 'exportEmployeesAll', 'format' => 'Excel', 'icon' => 'heroicon-o-users'],
                    ['title' => 'الموظفون النشطون', 'desc' => 'الموظفون في حالة نشطة فقط', 'method' => 'exportEmployeesActive', 'format' => 'Excel', 'icon' => 'heroicon-o-check-circle'],
                    ['title' => 'الموظفون في إجازة', 'desc' => 'قائمة الموظفين في إجازة', 'method' => 'exportEmployeesOnLeave', 'format' => 'Excel', 'icon' => 'heroicon-o-pause-circle'],
                    ['title' => 'الموظفون منتهي الخدمة', 'desc' => 'قائمة الموظفين السابقين', 'method' => 'exportEmployeesTerminated', 'format' => 'Excel', 'icon' => 'heroicon-o-x-circle'],
                ],

                'العملاء' => [
                    ['title' => 'جميع العملاء', 'desc' => 'كل العملاء بكل الأنواع', 'method' => 'exportCustomers', 'format' => 'Excel', 'icon' => 'heroicon-o-briefcase'],
                ],

                'الأرشيف' => [
                    ['title' => 'الوارد — جميع', 'desc' => 'كل الكتب الواردة', 'method' => 'exportIncomingAll', 'format' => 'Excel', 'icon' => 'heroicon-o-inbox-arrow-down'],
                    ['title' => 'الوارد المفتوح', 'desc' => 'الكتب الواردة المفتوحة فقط', 'method' => 'exportIncomingOpen', 'format' => 'Excel', 'icon' => 'heroicon-o-folder-open'],
                    ['title' => 'الصادر — جميع', 'desc' => 'كل الكتب الصادرة', 'method' => 'exportOutgoingAll', 'format' => 'Excel', 'icon' => 'heroicon-o-paper-airplane'],
                    ['title' => 'الصادر المُرسل', 'desc' => 'الكتب المُرسلة فقط', 'method' => 'exportOutgoingSent', 'format' => 'Excel', 'icon' => 'heroicon-o-check-badge'],
                ],

                'العهد والمقتنيات' => [
                    ['title' => 'جميع العهد', 'desc' => 'قائمة كاملة بالعهد', 'method' => 'exportCustodiesAll', 'format' => 'Excel', 'icon' => 'heroicon-o-shield-check'],
                    ['title' => 'العهد المسلَّمة', 'desc' => 'العهد المسلَّمة حالياً', 'method' => 'exportCustodiesDelivered', 'format' => 'Excel', 'icon' => 'heroicon-o-arrow-up-on-square'],
                    ['title' => 'العهد المُستردَّة', 'desc' => 'العهد التي تم استرجاعها', 'method' => 'exportCustodiesReturned', 'format' => 'Excel', 'icon' => 'heroicon-o-arrow-uturn-left'],
                ],

                'الرواتب' => [
                    ['title' => 'رواتب الشهر الحالي', 'desc' => 'رواتب '.now()->translatedFormat('F Y'), 'method' => 'exportSalariesCurrent', 'format' => 'Excel', 'icon' => 'heroicon-o-banknotes'],
                    ['title' => 'رواتب الشهر الماضي', 'desc' => 'رواتب '.now()->subMonth()->translatedFormat('F Y'), 'method' => 'exportSalariesLast', 'format' => 'Excel', 'icon' => 'heroicon-o-clock'],
                    ['title' => 'رواتب السنة الحالية', 'desc' => 'كل رواتب '.now()->year, 'method' => 'exportSalariesYear', 'format' => 'Excel', 'icon' => 'heroicon-o-calendar'],
                ],

                'المحاسبة والمالية' => [
                    ['title' => 'حركات اليوم', 'desc' => 'كل الحركات لتاريخ اليوم', 'method' => 'exportTxToday', 'format' => 'Excel', 'icon' => 'heroicon-o-calendar-days'],
                    ['title' => 'حركات الشهر', 'desc' => 'كل الحركات للشهر الحالي', 'method' => 'exportTxMonth', 'format' => 'Excel', 'icon' => 'heroicon-o-calendar'],
                    ['title' => 'حركات السنة', 'desc' => 'كل الحركات للسنة الحالية', 'method' => 'exportTxYear', 'format' => 'Excel', 'icon' => 'heroicon-o-rectangle-stack'],
                    ['title' => 'الإيرادات فقط', 'desc' => 'كل الإيرادات المؤكدة', 'method' => 'exportTxIncome', 'format' => 'Excel', 'icon' => 'heroicon-o-arrow-trending-up'],
                    ['title' => 'المصروفات فقط', 'desc' => 'كل المصروفات', 'method' => 'exportTxExpense', 'format' => 'Excel', 'icon' => 'heroicon-o-arrow-trending-down'],
                    ['title' => 'التقرير المالي الشامل', 'desc' => 'مستند Word جاهز للطباعة', 'method' => 'exportFinancialWord', 'format' => 'Word', 'icon' => 'heroicon-o-document-text'],
                ],
            ],
        ];
    }

    public function exportEmployeesAll()         { return Excel::download(new EmployeesExport, 'employees-all-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportEmployeesActive()      { return Excel::download(new EmployeesExport('active'), 'employees-active-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportEmployeesOnLeave()     { return Excel::download(new EmployeesExport('on_leave'), 'employees-on-leave-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportEmployeesTerminated()  { return Excel::download(new EmployeesExport('terminated'), 'employees-terminated-'.now()->format('Y-m-d').'.xlsx'); }

    public function exportCustomers()            { return Excel::download(new CustomersExport, 'customers-'.now()->format('Y-m-d').'.xlsx'); }

    public function exportIncomingAll()          { return Excel::download(new LettersExport('incoming'), 'incoming-all-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportIncomingOpen()         { return Excel::download(new LettersExport('incoming', 'open'), 'incoming-open-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportOutgoingAll()          { return Excel::download(new LettersExport('outgoing'), 'outgoing-all-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportOutgoingSent()         { return Excel::download(new LettersExport('outgoing', 'sent'), 'outgoing-sent-'.now()->format('Y-m-d').'.xlsx'); }

    public function exportCustodiesAll()         { return Excel::download(new CustodiesExport, 'custodies-all-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportCustodiesDelivered()   { return Excel::download(new CustodiesExport('delivered'), 'custodies-delivered-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportCustodiesReturned()    { return Excel::download(new CustodiesExport('returned'), 'custodies-returned-'.now()->format('Y-m-d').'.xlsx'); }

    public function exportSalariesCurrent()      { return Excel::download(new SalariesExport(now()->year, now()->month), 'salaries-'.now()->format('Y-m').'.xlsx'); }
    public function exportSalariesLast()         { return Excel::download(new SalariesExport(now()->subMonth()->year, now()->subMonth()->month), 'salaries-'.now()->subMonth()->format('Y-m').'.xlsx'); }
    public function exportSalariesYear()         { return Excel::download(new SalariesExport(now()->year), 'salaries-year-'.now()->year.'.xlsx'); }

    public function exportTxToday()              { return Excel::download(new TransactionsExport(today()->format('Y-m-d'), today()->format('Y-m-d')), 'transactions-today-'.today()->format('Y-m-d').'.xlsx'); }
    public function exportTxMonth()              { return Excel::download(new TransactionsExport(now()->startOfMonth()->format('Y-m-d'), now()->endOfMonth()->format('Y-m-d')), 'transactions-'.now()->format('Y-m').'.xlsx'); }
    public function exportTxYear()               { return Excel::download(new TransactionsExport(now()->startOfYear()->format('Y-m-d'), now()->endOfYear()->format('Y-m-d')), 'transactions-year-'.now()->year.'.xlsx'); }
    public function exportTxIncome()             { return Excel::download(new TransactionsExport(null, null, 'income'), 'income-'.now()->format('Y-m-d').'.xlsx'); }
    public function exportTxExpense()            { return Excel::download(new TransactionsExport(null, null, 'expense'), 'expense-'.now()->format('Y-m-d').'.xlsx'); }

    public function exportFinancialWord()
    {
        return redirect()->route('reports.financial-word');
    }
}
