<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'basic' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime' => 'decimal:2',
        'advances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net' => 'decimal:2',
        'paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Salary $salary) {
            $salary->net = ($salary->basic + $salary->allowances + $salary->overtime)
                - ($salary->advances + $salary->deductions);
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        return sprintf('%04d-%02d', $this->year, $this->month);
    }
}
