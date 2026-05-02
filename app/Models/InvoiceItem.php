<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::saving(function (InvoiceItem $item) {
            $item->total = (float) $item->quantity * (float) $item->unit_price;
        });

        static::saved(fn (InvoiceItem $item) => $item->invoice?->recalculate());
        static::deleted(fn (InvoiceItem $item) => $item->invoice?->recalculate());
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
