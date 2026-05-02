<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $income = Transaction::where('type', 'income')->where('status', 'confirmed')->sum('amount');
        $expense = Transaction::where('type', 'expense')->where('status', 'confirmed')->sum('amount');
        $net = $income - $expense;

        return [
            Stat::make('الموظفون', Employee::count())
                ->description(Employee::where('status', 'active')->count().' نشط')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('العملاء', Customer::count())
                ->description(Customer::where('status', 'active')->count().' عقود نشطة')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('info'),

            Stat::make('الوارد / الصادر', IncomingLetter::count().' / '.OutgoingLetter::count())
                ->description('إجمالي المراسلات')
                ->descriptionIcon('heroicon-m-inbox')
                ->color('warning'),

            Stat::make('صافي الحركات', number_format($net, 2).' ₪')
                ->description('إيرادات: '.number_format($income, 2).' | مصروفات: '.number_format($expense, 2))
                ->descriptionIcon($net >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($net >= 0 ? 'success' : 'danger'),
        ];
    }
}
