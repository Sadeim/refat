<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTrip extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'trip_date'       => 'date',
        'start_time'      => 'datetime:H:i',
        'end_time'        => 'datetime:H:i',
        'odometer_start'  => 'decimal:2',
        'odometer_end'    => 'decimal:2',
        'distance_km'     => 'decimal:2',
        'fuel_liters'     => 'decimal:2',
        'fuel_cost'       => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (VehicleTrip $t) {
            // حساب المسافة تلقائياً
            if (!is_null($t->odometer_start) && !is_null($t->odometer_end)) {
                $t->distance_km = max(0, round((float) $t->odometer_end - (float) $t->odometer_start, 2));
            }

            // اسم السائق الموظف
            if ($t->driver_id && empty($t->driver_name) && ($emp = Employee::find($t->driver_id))) {
                $t->driver_name = $emp->name_ar;
            }
        });

        static::saved(function (VehicleTrip $t) {
            // تحديث عداد السيارة الحالي إن كان أكبر
            if ($t->odometer_end && $t->vehicle && $t->odometer_end > $t->vehicle->current_odometer) {
                $t->vehicle->forceFill(['current_odometer' => $t->odometer_end])->save();
            }
        });
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (!$this->start_time || !$this->end_time) return null;
        return $this->start_time->diffInMinutes($this->end_time);
    }
}
