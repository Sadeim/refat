<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(
        protected ?string $from = null,
        protected ?string $to = null,
        protected ?string $type = null,
    ) {}

    public function collection()
    {
        return Transaction::query()
            ->when($this->from, fn ($q) => $q->whereDate('transaction_date', '>=', $this->from))
            ->when($this->to, fn ($q) => $q->whereDate('transaction_date', '<=', $this->to))
            ->when($this->type, fn ($q) => $q->where('type', $this->type))
            ->orderBy('transaction_date')
            ->get();
    }

    public function headings(): array
    {
        return ['الرقم المرجعي', 'النوع', 'التصنيف', 'المبلغ', 'العملة', 'التاريخ', 'الجهة', 'الوصف', 'الحالة'];
    }

    public function map($t): array
    {
        return [
            $t->reference_no,
            Transaction::TYPES[$t->type] ?? $t->type,
            Transaction::CATEGORIES[$t->category] ?? $t->category,
            $t->amount,
            $t->currency,
            optional($t->transaction_date)->format('Y-m-d'),
            $t->party?->name_ar ?? '—',
            $t->description,
            ['pending'=>'قيد التأكيد','confirmed'=>'مؤكدة','cancelled'=>'ملغاة'][$t->status] ?? $t->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string
    {
        return 'الحركات المالية';
    }
}
