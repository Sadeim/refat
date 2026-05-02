<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Customer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name_ar', 'phone', 'type', 'status', 'contract_value', 'contract_end'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $guarded = ['id'];

    protected $casts = [
        'contract_start' => 'date',
        'contract_end' => 'date',
        'contract_value' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->qr_token)) {
                $customer->qr_token = (string) Str::uuid();
            }
            if (empty($customer->code)) {
                $customer->code = 'CUS-'.str_pad((string) (static::max('id') + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('documents');
    }

    public function services(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }

    public function custodies(): MorphMany
    {
        return $this->morphMany(Custody::class, 'assigned_to');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_ar ?: ($this->name_en ?: $this->code);
    }
}
