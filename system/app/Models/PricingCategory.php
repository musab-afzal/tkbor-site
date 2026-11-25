<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'description', 'sort_order', 'status'])]
class PricingCategory extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return ['status' => 'boolean', 'sort_order' => 'integer'];
    }

    protected static function booted(): void
    {
        static::creating(function (PricingCategory $category) {
            $category->slug = $category->slug ?: Str::slug($category->name);
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(PricingItem::class);
    }
}
