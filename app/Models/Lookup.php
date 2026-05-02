<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lookup extends Model
{
    use HasFactory;

    public const TYPE_CUSTOMER     = 'customer_type';
    public const TYPE_CUSTODY      = 'custody_type';
    public const TYPE_EXPENSE_CAT  = 'expense_category';
    public const TYPE_INCOME_CAT   = 'income_category';
    public const TYPE_INVOICE_CAT  = 'invoice_category';

    public const TYPE_LABELS = [
        self::TYPE_CUSTOMER    => 'أنواع العملاء',
        self::TYPE_CUSTODY     => 'أنواع العهد والمقتنيات',
        self::TYPE_EXPENSE_CAT => 'تصنيفات المصروفات',
        self::TYPE_INCOME_CAT  => 'تصنيفات الإيرادات',
        self::TYPE_INVOICE_CAT => 'تصنيفات الفواتير',
    ];

    protected $fillable = [
        'type', 'key', 'label_ar', 'label_en', 'color', 'icon', 'is_active', 'sort', 'meta',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'meta' => 'array',
    ];

    public function scopeOfType(Builder $q, string $type): Builder
    {
        return $q->where('type', $type);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /** Returns ['key' => 'label_ar'] for a given type — for select() options */
    public static function options(string $type): array
    {
        return static::ofType($type)->active()->orderBy('sort')->orderBy('id')->pluck('label_ar', 'key')->toArray();
    }

    /** Look up a single label by key, with fallback */
    public static function label(string $type, ?string $key, ?string $fallback = null): string
    {
        if (!$key) return $fallback ?? '—';
        return static::ofType($type)->where('key', $key)->value('label_ar') ?? $fallback ?? $key;
    }
}
