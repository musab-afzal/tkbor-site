<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * The reusable service/module price book. Quotations and invoices pull from it,
 * then keep their own editable copy of the values on the line item.
 */
#[Fillable([
    'pricing_category_id', 'service_id', 'name', 'slug', 'short_description',
    'detailed_features', 'default_price', 'min_price', 'max_price', 'internal_cost',
    'unit', 'estimated_days', 'technologies', 'is_taxable', 'discount_allowed',
    'is_optional', 'is_free', 'addons', 'notes', 'sort_order', 'status',
])]
class PricingItem extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const UNITS = [
        'fixed' => 'Fixed',
        'hour' => 'Per Hour',
        'day' => 'Per Day',
        'month' => 'Per Month',
        'project' => 'Per Project',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'addons' => 'array',
            'default_price' => 'decimal:2',
            'min_price' => 'decimal:2',
            'max_price' => 'decimal:2',
            'internal_cost' => 'decimal:2',
            'is_taxable' => 'boolean',
            'discount_allowed' => 'boolean',
            'is_optional' => 'boolean',
            'is_free' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (PricingItem $item) {
            $item->slug = $item->slug ?: static::uniqueSlug($item->name);
        });
    }

    protected static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'item';
        $slug = $base;
        $suffix = 2;

        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PricingCategory::class, 'pricing_category_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
