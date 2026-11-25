<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Lightweight internal to-do list, deliberately separate from formal project
 * tasks.
 */
#[Fillable([
    'title', 'description', 'assigned_to', 'date', 'due_date', 'priority',
    'status', 'is_company_wide', 'notes',
])]
class Todo extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'due_date' => 'date',
            'is_company_wide' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Todo $todo) {
            if ($todo->status === self::STATUS_COMPLETED) {
                $todo->completed_at = $todo->completed_at ?: now();
            } elseif ($todo->isDirty('status')) {
                $todo->completed_at = null;
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TodoItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_COMPLETED, 'cancelled']);
    }

    /** Mine = created by me or assigned to me. */
    public function scopeVisibleTo(Builder $query, ?Admin $admin): Builder
    {
        return $query->where(function (Builder $q) use ($admin) {
            $q->where('is_company_wide', true)
                ->orWhere('created_by', $admin?->getKey())
                ->orWhere('assigned_to', $admin?->getKey());
        });
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_COMPLETED
            && $this->due_date
            && $this->due_date->isPast();
    }
}
