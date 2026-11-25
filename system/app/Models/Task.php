<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\HasComments;
use App\Models\Concerns\HasTags;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A task may hang off a project, a client, a department or nothing at all
 * (a general company task).
 */
#[Fillable([
    'title', 'description', 'project_id', 'client_id', 'department_id', 'milestone_id',
    'assigned_by', 'priority', 'status', 'start_date', 'due_date', 'due_time',
    'estimated_hours', 'actual_hours', 'progress', 'sort_order',
])]
class Task extends Model
{
    use HasAttachments, HasComments, HasTags, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_TODO = 'todo';

    public const STATUS_IN_PROGRESS = 'in_progress';

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'review' => 'Review',
        'completed' => 'Completed',
        'on_hold' => 'On Hold',
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
            'start_date' => 'date',
            'due_date' => 'date',
            'estimated_hours' => 'decimal:2',
            'actual_hours' => 'decimal:2',
            'progress' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->task_number = $task->task_number
                ?: app(DocumentNumberGenerator::class)->next('tasks', 'task_number', 'task_prefix', 'TSK');
        });

        static::saving(function (Task $task) {
            if ($task->status === self::STATUS_COMPLETED) {
                $task->completed_at = $task->completed_at ?: now();
                $task->progress = 100;
            } elseif ($task->isDirty('status')) {
                $task->completed_at = null;
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)->withPivot('is_primary');
    }

    public function checklistItems(): HasMany
    {
        return $this->hasMany(TaskChecklistItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function dependencies(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'task_dependencies', 'task_id', 'depends_on_task_id');
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', '!=', self::STATUS_COMPLETED);
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->open()->whereDate('due_date', today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->open()->whereDate('due_date', '<', today());
    }

    public function scopeUpcoming(Builder $query, int $days = 7): Builder
    {
        return $query->open()->whereBetween('due_date', [today()->addDay(), today()->addDays($days)]);
    }

    /** Tasks assigned to the employee linked to the given admin account. */
    public function scopeAssignedToAdmin(Builder $query, ?Admin $admin): Builder
    {
        return $query->whereHas('assignees', fn (Builder $q) => $q->where('admin_id', $admin?->getKey()));
    }

    public function isOverdue(): bool
    {
        return $this->status !== self::STATUS_COMPLETED
            && $this->due_date
            && $this->due_date->isPast();
    }

    public function isDueToday(): bool
    {
        return $this->status !== self::STATUS_COMPLETED
            && $this->due_date
            && $this->due_date->isToday();
    }

    /** Percentage of checklist items ticked off, or null when there is no list. */
    public function checklistProgress(): ?int
    {
        $total = $this->checklistItems->count();

        if ($total === 0) {
            return null;
        }

        return (int) round($this->checklistItems->where('is_done', true)->count() / $total * 100);
    }

    public function activityLabel(): string
    {
        return $this->task_number.' — '.$this->title;
    }
}
