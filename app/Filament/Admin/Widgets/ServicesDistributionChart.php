<?php

namespace App\Filament\Admin\Widgets;

use App\Models\CustomerService;
use Filament\Widgets\ChartWidget;

class ServicesDistributionChart extends ChartWidget
{
    protected ?string $heading = 'توزيع الخدمات';

    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $rows = CustomerService::query()
            ->selectRaw('service_type, count(*) as cnt')
            ->groupBy('service_type')
            ->pluck('cnt', 'service_type');

        $labels = $rows->keys()->map(fn ($k) => CustomerService::TYPES[$k] ?? $k)->all();
        $data = $rows->values()->all();

        return [
            'datasets' => [
                [
                    'label' => 'عدد الخدمات',
                    'data' => $data,
                    'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
