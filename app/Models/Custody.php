<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Custody extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['asset_name', 'asset_type', 'status', 'assigned_to_type', 'assigned_to_id', 'returned_at'])
            ->logOnlyDirty();
    }

    public const ASSET_TYPES = [
        'weapon' => 'سلاح',
        'vehicle' => 'سيارة',
        'radio' => 'جهاز اتصال',
        'uniform' => 'زي رسمي',
        'equipment' => 'معدات',
        'other' => 'أخرى',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'value' => 'decimal:2',
        'delivered_at' => 'date',
        'returned_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Custody $custody) {
            if (empty($custody->reference_no)) {
                $lastRef = static::withTrashed()->where('reference_no', 'like', 'CTD-%')->orderByRaw('CAST(SUBSTR(reference_no, 5) AS INTEGER) DESC')->value('reference_no');
                $nextNumber = $lastRef ? ((int) substr($lastRef, 4)) + 1 : 1;
                $custody->reference_no = 'CTD-'.str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function assignedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
        $this->addMediaCollection('documents');
    }
}
