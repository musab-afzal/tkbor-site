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
    'title', 'slug', 'department_id', 'location', 'employment_type',
    'experience_level', 'salary_range', 'description', 'responsibilities',
    'requirements', 'closing_date', 'status',
])]
class Career extends Model
{
    use HasSlug, LogsActivity, SoftDeletes;

    public const EXPERIENCE_LEVELS = [
        'entry' => 'Entry Level',
        'junior' => 'Junior',
        'mid' => 'Mid Level',
        'senior' => 'Senior',
        'lead' => 'Lead',
    ];

    protected function casts(): array
    {
        return ['closing_date' => 'date', 'status' => 'boolean'];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', true)
            ->where(fn (Builder $q) => $q->whereNull('closing_date')->orWhereDate('closing_date', '>=', today()));
    }
}
