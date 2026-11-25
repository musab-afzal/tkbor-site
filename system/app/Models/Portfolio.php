<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Website portfolio entry. Previously the `projects` table — renamed to
 * `portfolios` so the delivery module could own the Project name. All existing
 * rows were carried over untouched.
 */
#[Fillable([
    'title', 'slug', 'client_name', 'category', 'short_description', 'description',
    'featured_image', 'project_url', 'technologies', 'project_date', 'status', 'is_featured',
])]
class Portfolio extends Model
{
    use HasSlug, LogsActivity;

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'project_date' => 'date',
            'status' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function caseStudies(): HasMany
    {
        return $this->hasMany(CaseStudy::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
