<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WagePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'year', 'month',
        'total_hours', 'total_amount',
        'work_days', 'absence_days', 'leave_days',
        'paid_at', 'payment_method', 'transaction_id', 'paid_by', 'notes',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'total_hours' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public const MONTHS_AR = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس',  4 => 'أبريل',
        5 => 'مايو',  6 => 'يونيو',  7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'paid_by');
    }

    public function getPeriodLabelAttribute(): string
    {
        return (self::MONTHS_AR[$this->month] ?? $this->month).' '.$this->year;
    }

    public function getIsPaidAttribute(): bool
    {
        return !is_null($this->paid_at);
    }

    /**
     * Compute (or refresh) the monthly aggregate from attendances.
     * Returns the WagePayment record (existing or new).
     */
    public static function buildFromAttendances(int $employeeId, int $year, int $month): self
    {
        $records = Attendance::where('employee_id', $employeeId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalHours  = (float) $records->sum('hours');
        $totalAmount = (float) $records->sum('daily_total');
        $workDays    = $records->where('status', 'present')->count();
        $absenceDays = $records->where('status', 'absent')->count();
        $leaveDays   = $records->where('status', 'leave')->count();

        $wp = self::firstOrNew([
            'employee_id' => $employeeId,
            'year'        => $year,
            'month'       => $month,
        ]);

        // لا نُحدّث الأجر بعد الدفع، نحافظ على الأرشيف
        if (is_null($wp->paid_at)) {
            $wp->total_hours  = $totalHours;
            $wp->total_amount = $totalAmount;
            $wp->work_days    = $workDays;
            $wp->absence_days = $absenceDays;
            $wp->leave_days   = $leaveDays;
            $wp->save();
        }

        return $wp;
    }

    /**
     * Mark this wage payment as paid; creates a linked expense Transaction.
     */
    public function markAsPaid(?string $method = 'cash', ?string $notes = null): self
    {
        if ($this->is_paid) {
            return $this;
        }

        $employee = $this->employee;

        $tx = Transaction::create([
            'reference_no'     => 'WAGE-'.now()->format('ymd').'-'.$this->id,
            'type'             => 'expense',
            'category'         => 'salary',
            'amount'           => $this->total_amount,
            'currency'         => 'ILS',
            'transaction_date' => now()->toDateString(),
            'party_type'       => 'employee',
            'party_id'         => $employee?->id,
            'payment_method'   => $method,
            'description'      => 'دفع أجر شهر '.$this->period_label.' للموظف '.($employee?->name_ar ?? '-'),
            'status'           => 'confirmed',
            'created_by'       => auth()->id(),
        ]);

        $this->forceFill([
            'paid_at'        => now()->toDateString(),
            'payment_method' => $method,
            'transaction_id' => $tx->id,
            'paid_by'        => auth()->id(),
            'notes'          => $notes ?? $this->notes,
        ])->save();

        return $this;
    }
}
