<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Transaction extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty();
    }

    public const TYPES = [
        'income' => 'إيراد',
        'expense' => 'مصروف',
    ];

    public const CATEGORIES = [
        'salary' => 'رواتب',
        'service_revenue' => 'إيراد خدمات',
        'rent' => 'إيجار',
        'utilities' => 'فواتير ومرافق',
        'fuel' => 'وقود',
        'maintenance' => 'صيانة',
        'office' => 'مصاريف مكتب',
        'other' => 'أخرى',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            if (empty($transaction->reference_no)) {
                $year = now()->year;
                $next = static::whereYear('created_at', $year)->count() + 1;
                $prefix = $transaction->type === 'income' ? 'INC' : 'EXP';
                $transaction->reference_no = "{$prefix}-{$year}-".str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function party(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }
}
