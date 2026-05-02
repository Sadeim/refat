<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use App\Models\Custody;
use App\Models\Employee;
use App\Models\Salary;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SmartAlerts extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $expiringContracts = Customer::whereBetween('contract_end', [today(), today()->addDays(30)])->count();
        $overdueCustodies = Custody::where('status', 'delivered')
            ->where('delivered_at', '<', today()->subDays(90))
            ->count();
        $birthdays = Employee::whereNotNull('dob')
            ->whereMonth('dob', now()->month)
            ->count();
        $unpaidSalaries = Salary::whereIn('status', ['draft', 'approved'])->count();

        return [
            Stat::make('عقود تنتهي خلال 30 يوم', $expiringContracts)
                ->description($expiringContracts > 0 ? 'يحتاج متابعة' : 'لا يوجد')
                ->color($expiringContracts > 0 ? 'warning' : 'success')
                ->descriptionIcon('heroicon-m-exclamation-triangle'),

            Stat::make('عهد غير مُستردَّة منذ 90 يوم', $overdueCustodies)
                ->description($overdueCustodies > 0 ? 'تحقق منها' : 'لا يوجد')
                ->color($overdueCustodies > 0 ? 'danger' : 'success')
                ->descriptionIcon('heroicon-m-shield-exclamation'),

            Stat::make('أعياد ميلاد هذا الشهر', $birthdays)
                ->description($birthdays > 0 ? 'موظفون يحتفلون' : 'لا يوجد')
                ->color($birthdays > 0 ? 'info' : 'gray')
                ->descriptionIcon('heroicon-m-cake'),

            Stat::make('رواتب لم تُصرَف', $unpaidSalaries)
                ->description($unpaidSalaries > 0 ? 'معلقة' : 'الكل مدفوع')
                ->color($unpaidSalaries > 0 ? 'warning' : 'success')
                ->descriptionIcon('heroicon-m-banknotes'),
        ];
    }
}
