<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FixedExpense extends Model
{
    use HasFactory;

    public const FREQUENCIES = [
        'monthly' => 'شهرياً',
        'yearly'  => 'سنوياً',
        'weekly'  => 'أسبوعياً',
    ];

    protected $fillable = [
        'name', 'category', 'amount', 'currency', 'frequency', 'day_of_period',
        'start_date', 'end_date', 'last_run_at', 'next_run_at',
        'is_active', 'auto_post', 'payment_method', 'notes', 'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'last_run_at' => 'date',
        'next_run_at' => 'date',
        'is_active' => 'boolean',
        'auto_post' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (FixedExpense $fe) {
            $fe->next_run_at = $fe->next_run_at ?? $fe->computeNextRun($fe->start_date);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function computeNextRun(?\Carbon\Carbon $from = null): \Carbon\Carbon
    {
        $from = $from ? $from->copy() : now();

        return match ($this->frequency) {
            'yearly' => $from->copy()->addYear()->day(min($this->day_of_period ?: 1, 28)),
            'weekly' => $from->copy()->addWeek(),
            default  => $from->copy()->addMonth()->day(min($this->day_of_period ?: 1, 28)),
        };
    }

    /** Generate a Transaction from this fixed expense */
    public function postTransaction(): Transaction
    {
        $tx = Transaction::create([
            'reference_no'     => 'FX-'.now()->format('ymdHis').'-'.$this->id,
            'type'             => 'expense',
            'category'         => $this->category,
            'amount'           => $this->amount,
            'currency'         => $this->currency,
            'transaction_date' => now()->toDateString(),
            'payment_method'   => $this->payment_method,
            'description'      => 'مصروف ثابت: '.$this->name,
            'status'           => 'confirmed',
            'created_by'       => auth()->id(),
        ]);

        $this->forceFill([
            'last_run_at' => now()->toDateString(),
            'next_run_at' => $this->computeNextRun(now()),
        ])->save();

        return $tx;
    }
}
