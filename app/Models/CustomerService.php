<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerService extends Model
{
    use HasFactory;

    public const TYPES = [
        'cars' => 'حماية سيارات',
        'weapons' => 'حماية أسلحة',
        'personal_security' => 'تأمين شخصي',
        'logistics' => 'لوجستيات',
        'other' => 'خدمة أخرى',
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function getServiceTypeLabelAttribute(): string
    {
        return self::TYPES[$this->service_type] ?? $this->service_type;
    }
}
