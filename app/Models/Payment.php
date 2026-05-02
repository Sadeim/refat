<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const METHODS = [
        'cash' => 'نقداً',
        'bank' => 'تحويل بنكي',
        'cheque' => 'شيك',
        'card' => 'بطاقة',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::saved(fn (Payment $p) => $p->invoice?->recalculate());
        static::deleted(fn (Payment $p) => $p->invoice?->recalculate());
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
