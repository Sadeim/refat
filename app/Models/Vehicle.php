<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Vehicle extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public const STATUSES = [
        'active'      => 'نشطة',
        'maintenance' => 'في الصيانة',
        'retired'     => 'متوقفة/خارج الخدمة',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'current_odometer' => 'decimal:2',
        'insurance_expiry' => 'date',
        'license_expiry'   => 'date',
    ];

    public function defaultDriver(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'default_driver_id');
    }

    public function trips(): HasMany
    {
        return $this->hasMany(VehicleTrip::class)->orderBy('trip_date', 'desc')->orderBy('start_time', 'desc');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('photos');
        $this->addMediaCollection('documents'); // license, insurance, inspection
    }

    public function getDisplayNameAttribute(): string
    {
        return trim($this->plate_number.' — '.($this->model ?? ''));
    }
}
