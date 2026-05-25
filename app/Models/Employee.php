<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Employee extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name_ar', 'phone', 'position', 'status', 'basic_salary'])
            ->logOnlyDirty();
    }

    protected $guarded = ['id'];

    protected $casts = [
        'specs' => 'array',
        'schedule' => 'array',
        'dob' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Employee $employee) {
            if (empty($employee->qr_token)) {
                $employee->qr_token = (string) Str::uuid();
            }
            if (empty($employee->code)) {
                $lastCode = static::withTrashed()->where('code', 'like', 'EMP-%')->orderByRaw('CAST(SUBSTR(code, 5) AS INTEGER) DESC')->value('code');
                $nextNumber = $lastCode ? ((int) substr($lastCode, 4)) + 1 : 1;
                $employee->code = 'EMP-'.str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photo')->singleFile();
        $this->addMediaCollection('documents');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(\Spatie\Image\Enums\Fit::Crop, 200, 200)
            ->nonQueued();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('photo', 'thumb') ?: null;
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->name_ar ?: ($this->name_en ?: $this->code);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function wagePayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WagePayment::class);
    }
}
