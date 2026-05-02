<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected ?string $status = null) {}

    public function collection()
    {
        return Employee::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'الكود', 'الاسم بالعربي', 'الاسم بالإنجليزي', 'الرقم الوطني',
            'الهاتف', 'البريد', 'المسمى', 'القسم',
            'تاريخ المباشرة', 'ساعات/يوم', 'الراتب الأساسي', 'البدلات', 'الحالة',
        ];
    }

    public function map($e): array
    {
        return [
            $e->code, $e->name_ar, $e->name_en, $e->national_id,
            $e->phone, $e->email, $e->position, $e->department,
            optional($e->start_date)->format('Y-m-d'),
            $e->daily_hours, $e->basic_salary, $e->allowances,
            ['active'=>'نشط','on_leave'=>'إجازة','suspended'=>'موقوف','terminated'=>'منتهي'][$e->status] ?? $e->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]],
        ];
    }

    public function title(): string
    {
        return 'الموظفون';
    }
}
