<?php

namespace App\Exports;

use App\Models\IncomingLetter;
use App\Models\OutgoingLetter;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LettersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected string $direction = 'incoming', protected ?string $status = null) {}

    public function collection()
    {
        $model = $this->direction === 'outgoing' ? OutgoingLetter::class : IncomingLetter::class;
        return $model::query()
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->orderBy('letter_date', 'desc')
            ->get();
    }

    public function headings(): array
    {
        $party = $this->direction === 'outgoing' ? 'إلى' : 'من';
        $dateLabel = $this->direction === 'outgoing' ? 'تاريخ الإرسال' : 'تاريخ الاستلام';
        return ['الرقم المرجعي', 'تاريخ الكتاب', $party, 'الموضوع', 'الأولوية', 'الحالة', $dateLabel];
    }

    public function map($l): array
    {
        $party = $this->direction === 'outgoing' ? $l->to_party : $l->from_party;
        $extraDate = $this->direction === 'outgoing' ? $l->sent_at : $l->received_at;
        $statusMap = $this->direction === 'outgoing'
            ? ['draft'=>'مسودة','sent'=>'مُرسل','delivered'=>'مُستلم','archived'=>'مؤرشف']
            : ['open'=>'مفتوح','in_progress'=>'قيد المعالجة','closed'=>'مغلق','archived'=>'مؤرشف'];

        return [
            $l->reference_no,
            optional($l->letter_date)->format('Y-m-d'),
            $party,
            $l->subject,
            ['low'=>'منخفضة','normal'=>'عادية','high'=>'عالية','urgent'=>'عاجل'][$l->priority] ?? $l->priority,
            $statusMap[$l->status] ?? $l->status,
            optional($extraDate)->format('Y-m-d'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string
    {
        return $this->direction === 'outgoing' ? 'الصادر' : 'الوارد';
    }
}
