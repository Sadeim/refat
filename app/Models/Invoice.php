<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Invoice extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    public const STATUSES = [
        'draft' => 'مسودة',
        'sent' => 'مُرسلة',
        'partial' => 'مدفوعة جزئياً',
        'paid' => 'مدفوعة',
        'overdue' => 'متأخرة',
        'cancelled' => 'ملغاة',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'paid_total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->number)) {
                $year = now()->year;
                $next = static::whereYear('created_at', $year)->count() + 1;
                $invoice->number = "INV-{$year}-".str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getRemainingAttribute(): float
    {
        return max(0, (float) $this->total - (float) $this->paid_total);
    }

    public function recalculate(): void
    {
        $subtotal = $this->items()->sum('total');
        $total = $subtotal + (float) $this->tax - (float) $this->discount;
        $paid = (float) $this->payments()->sum('amount');

        $status = $this->status;
        if ($status !== 'cancelled') {
            if ($paid <= 0) {
                $status = $this->status === 'draft' ? 'draft' : 'sent';
            } elseif ($paid < $total) {
                $status = 'partial';
            } else {
                $status = 'paid';
            }
        }

        $this->update([
            'subtotal' => $subtotal,
            'total' => $total,
            'paid_total' => $paid,
            'status' => $status,
        ]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
