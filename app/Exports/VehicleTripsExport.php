<?php

namespace App\Exports;

use App\Models\VehicleTrip;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VehicleTripsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(protected ?int $vehicleId = null, protected ?string $from = null, protected ?string $to = null) {}

    public function collection()
    {
        return VehicleTrip::query()
            ->with(['vehicle', 'customer'])
            ->when($this->vehicleId, fn ($q) => $q->where('vehicle_id', $this->vehicleId))
            ->when($this->from, fn ($q) => $q->whereDate('trip_date', '>=', $this->from))
            ->when($this->to,   fn ($q) => $q->whereDate('trip_date', '<=', $this->to))
            ->orderBy('trip_date')->orderBy('start_time')
            ->get();
    }

    public function headings(): array
    {
        return ['م', 'رقم السيارة', 'الموديل', 'اسم السائق', 'التاريخ', 'وقت الحركة', 'وقت الانتهاء', 'العداد بداية', 'العداد نهاية', 'كم المقطوعة', 'طبيعة المهمة', 'العميل', 'ملاحظات'];
    }

    private int $i = 0;

    public function map($t): array
    {
        return [
            ++$this->i,
            $t->vehicle?->plate_number,
            $t->vehicle?->model,
            $t->driver_name,
            optional($t->trip_date)->format('Y-m-d'),
            optional($t->start_time)->format('H:i'),
            optional($t->end_time)->format('H:i'),
            $t->odometer_start,
            $t->odometer_end,
            $t->distance_km,
            $t->purpose,
            $t->customer?->name_ar,
            $t->notes,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'D9E8FA']]]];
    }

    public function title(): string { return 'سجل حركة المركبات'; }
}
