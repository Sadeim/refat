<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRepresentative extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function booted(): void
    {
        // عند تعيين ممثل كرئيسي، إلغاء "رئيسي" عن باقي ممثلي نفس العميل
        static::saved(function (CustomerRepresentative $rep) {
            if ($rep->is_primary && $rep->wasChanged('is_primary')) {
                static::where('customer_id', $rep->customer_id)
                    ->where('id', '!=', $rep->id)
                    ->update(['is_primary' => false]);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
