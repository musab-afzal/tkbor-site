<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Package pricing shown on the public packages page and reusable inside
 * quotations.
 */
#[Fillable([
    'service_id', 'name', 'slug', 'tier', 'short_description', 'price',
    'discount_price', 'currency', 'billing_period', 'features', 'delivery_days',
    'revisions', 'is_recommended', 'sort_order', 'status',
])]
class Package extends Model
{
    use LogsActivity, SoftDeletes;

    public const TIERS = [
        'basic' => 'Basic',
        'starter' => 'Starter',
        'professional' => 'Professional',
        'business' => 'Business',
        'enterprise' => 'Enterprise',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'is_recommended' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            $package->slug = $package->slug ?: Str::slug($package->name).'-'.Str::lower(Str::random(4));
        });
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    /** What the client actually pays. */
    public function effectivePrice(): float
    {
        return (float) ($this->discount_price ?: $this->price);
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && (float) $this->discount_price < (float) $this->price;
    }
}
