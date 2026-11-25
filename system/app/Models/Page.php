<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'content', 'featured_image', 'meta_title',
    'meta_description', 'meta_keywords', 'status',
])]
class Page extends Model
{
    use HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return ['status' => 'boolean'];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }
}
