<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vacation extends Model
{
    public const TYPES = [
        'annual' => 'سنوية',
        'sick' => 'مرضية',
        'emergency' => 'طارئة',
        'maternity' => 'أمومة',
        'unpaid' => 'بدون راتب',
        'other' => 'أخرى',
    ];

    public const STATUSES = [
        'pending' => 'قيد الموافقة',
        'approved' => 'موافق عليها',
        'rejected' => 'مرفوضة',
        'cancelled' => 'ملغاة',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Vacation $v) {
            if ($v->start_date && $v->end_date) {
                $v->days = $v->start_date->diffInDays($v->end_date) + 1;
            }
        });
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
