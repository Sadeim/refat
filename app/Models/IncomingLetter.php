<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class IncomingLetter extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'letter_date' => 'date',
        'received_at' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (IncomingLetter $letter) {
            if (empty($letter->reference_no)) {
                $year = now()->year;
                $next = static::whereYear('created_at', $year)->count() + 1;
                $letter->reference_no = "IN-{$year}-".str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
