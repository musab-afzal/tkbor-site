<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'slug', 'short_description', 'content', 'featured_image',
    'author_name', 'published_at', 'status', 'meta_title', 'meta_description',
])]
class Blog extends Model
{
    use HasSlug, LogsActivity, SoftDeletes;

    public const STATUS_PUBLISHED = 'published';

    public const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(fn (Builder $q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}
