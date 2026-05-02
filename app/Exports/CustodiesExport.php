<?php

namespace App\Exports;

use App\Models\Custody;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustodiesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected ?string $status = null) {}

    public function collection()
    {
        return Custody::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy('id')
            ->get();
    }

    public function headings(): array
    {
        return ['رقم العهدة', 'النوع', 'الاسم', 'الرقم التسلسلي', 'القيمة', 'المسلَّم إليه', 'تاريخ التسليم', 'تاريخ الاسترجاع', 'الحالة'];
    }

    public function map($c): array
    {
        return [
            $c->reference_no,
            Custody::ASSET_TYPES[$c->asset_type] ?? $c->asset_type,
            $c->asset_name,
            $c->serial_no,
            $c->value,
            ['employee'=>'موظف','customer'=>'عميل'][$c->assigned_to_type] ?? $c->assigned_to_type,
            optional($c->delivered_at)->format('Y-m-d'),
            optional($c->returned_at)->format('Y-m-d'),
            ['delivered'=>'مسلَّمة','returned'=>'مُستردَّة','lost'=>'مفقودة','damaged'=>'تالفة'][$c->status] ?? $c->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string { return 'العهد'; }
}
