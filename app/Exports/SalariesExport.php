<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalariesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected ?int $year = null, protected ?int $month = null) {}

    public function collection()
    {
        return Salary::query()->with('employee')
            ->when($this->year, fn ($q) => $q->where('year', $this->year))
            ->when($this->month, fn ($q) => $q->where('month', $this->month))
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return ['الموظف', 'السنة', 'الشهر', 'الأساسي', 'البدلات', 'إضافي', 'سُلَف', 'خصومات', 'الصافي', 'الحالة', 'تاريخ الصرف'];
    }

    public function map($s): array
    {
        $months = [1=>'يناير',2=>'فبراير',3=>'مارس',4=>'أبريل',5=>'مايو',6=>'يونيو',7=>'يوليو',8=>'أغسطس',9=>'سبتمبر',10=>'أكتوبر',11=>'نوفمبر',12=>'ديسمبر'];
        return [
            $s->employee?->name_ar,
            $s->year,
            $months[$s->month] ?? $s->month,
            $s->basic, $s->allowances, $s->overtime,
            $s->advances, $s->deductions, $s->net,
            ['draft'=>'مسودة','approved'=>'معتمد','paid'=>'مدفوع'][$s->status] ?? $s->status,
            optional($s->paid_at)->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string
    {
        return 'الرواتب';
    }
}
