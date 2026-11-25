<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'client_id', 'portfolio_id', 'industry', 'short_description',
    'challenge', 'solution', 'results', 'technologies', 'featured_image',
    'meta_title', 'meta_description', 'is_featured', 'sort_order', 'status',
])]
class CaseStudy extends Model
{
    use HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'is_featured' => 'boolean',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
