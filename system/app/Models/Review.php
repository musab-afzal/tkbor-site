<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/** Website testimonials. */
#[Fillable([
    'client_name', 'company_name', 'project_name', 'designation', 'review', 'rating',
    'client_image', 'status', 'sort_order',
])]
class Review extends Model
{
    use LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function activityLabel(): string
    {
        return $this->client_name;
    }
}
