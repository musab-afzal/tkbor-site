<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'short_description', 'description', 'benefits', 'features',
    'cta_label', 'cta_url', 'icon', 'image', 'status', 'sort_order',
])]
class Service extends Model
{
    use HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'features' => 'array',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function pricingItems(): HasMany
    {
        return $this->hasMany(PricingItem::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
