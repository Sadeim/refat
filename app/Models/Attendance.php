<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const STATUSES = [
        'present' => 'دوام',
        'late'    => 'متأخر',
        'absent'  => 'غياب',
        'half_day'=> 'نصف يوم',
        'leave'   => 'إجازة',
    ];

    public const PERIODS = [
        'morning' => 'صباحية',
        'evening' => 'مسائية',
        'night'   => 'ليلية',
        'full'    => 'يوم كامل',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
        'hours' => 'decimal:2',
        'hourly_rate' => 'decimal:2',
        'daily_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // Auto-compute daily_total = hours × hourly_rate on save
        static::saving(function (Attendance $a) {
            if (empty($a->hourly_rate) && $a->employee) {
                $a->hourly_rate = $a->employee->hourly_rate ?? 0;
            }
            if (empty($a->work_location) && $a->employee) {
                $a->work_location = $a->employee->work_location;
            }
            $a->daily_total = round((float) $a->hours * (float) $a->hourly_rate, 2);
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'supervisor_id');
    }
}
