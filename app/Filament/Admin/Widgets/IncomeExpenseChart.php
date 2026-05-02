<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;

class IncomeExpenseChart extends ChartWidget
{
    protected ?string $heading = 'الإيرادات والمصروفات — آخر 6 أشهر';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $incomes[] = (float) Transaction::where('type', 'income')
                ->where('status', 'confirmed')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
            $expenses[] = (float) Transaction::where('type', 'expense')
                ->where('status', 'confirmed')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'الإيرادات (₪)',
                    'data' => $incomes,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, .15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'المصروفات (₪)',
                    'data' => $expenses,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, .15)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
