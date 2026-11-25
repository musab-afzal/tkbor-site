<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'seo_project_id', 'keyword', 'target_url', 'search_volume', 'current_rank',
    'previous_rank', 'last_checked_at', 'difficulty', 'notes', 'status',
])]
class Keyword extends Model
{
    use LogsActivity, SoftDeletes;

    public const STATUSES = [
        'tracking' => 'Tracking',
        'paused' => 'Paused',
        'achieved' => 'Target Achieved',
        'dropped' => 'Dropped',
    ];

    public const DIFFICULTIES = [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
    ];

    protected function casts(): array
    {
        return ['last_checked_at' => 'date'];
    }

    public function seoProject(): BelongsTo
    {
        return $this->belongsTo(SeoProject::class);
    }

    /** Positive means the keyword climbed since the previous check. */
    public function rankChange(): ?int
    {
        if ($this->current_rank === null || $this->previous_rank === null) {
            return null;
        }

        return $this->previous_rank - $this->current_rank;
    }
}
