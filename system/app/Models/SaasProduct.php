<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/** A piece of software offered on the public SaaS page with a free trial. */
#[Fillable([
    'name', 'slug', 'short_description', 'description', 'image', 'features',
    'app_url', 'price', 'currency', 'billing_period', 'billing_days',
    'trial_days', 'status', 'sort_order',
])]
class SaasProduct extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'price' => 'decimal:2',
            'billing_days' => 'integer',
            'trial_days' => 'integer',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (SaasProduct $product) {
            if (blank($product->slug)) {
                $base = Str::slug($product->name) ?: 'software';
                $slug = $base;
                $i = 2;

                while (static::withTrashed()->where('slug', $slug)->whereKeyNot($product->id)->exists()) {
                    $slug = $base.'-'.$i++;
                }

                $product->slug = $slug;
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(SaasSubscription::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function trialLength(): int
    {
        return max(1, (int) ($this->trial_days ?: 7));
    }

    public function billingLength(): int
    {
        return max(1, (int) ($this->billing_days ?: 30));
    }
}
