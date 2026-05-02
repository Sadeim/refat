<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Customer::query()->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'الكود', 'الاسم', 'النوع', 'الهاتف', 'البريد',
            'شخص الاتصال', 'هاتف الاتصال', 'بداية العقد', 'نهاية العقد',
            'قيمة العقد', 'الحالة',
        ];
    }

    public function map($c): array
    {
        return [
            $c->code, $c->name_ar,
            ['individual'=>'فرد','company'=>'شركة','government'=>'حكومي','embassy'=>'سفارة','organization'=>'منظمة'][$c->type] ?? $c->type,
            $c->phone, $c->email, $c->contact_person, $c->contact_phone,
            optional($c->contract_start)->format('Y-m-d'),
            optional($c->contract_end)->format('Y-m-d'),
            $c->contract_value,
            ['active'=>'نشط','paused'=>'موقوف','expired'=>'منتهٍ'][$c->status] ?? $c->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string
    {
        return 'العملاء';
    }
}
