<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
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
 * A delivery project for a client. The website portfolio lives in Portfolio.
 */
#[Fillable([
    'client_id', 'quotation_id', 'lead_id', 'name', 'type', 'description',
    'project_manager_id', 'technologies', 'start_date', 'deadline', 'completed_at',
    'budget', 'currency', 'progress', 'status', 'priority', 'repository_url',
    'live_url', 'staging_url', 'notes',
])]
class Project extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_PLANNING = 'planning';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        'planning' => 'Planning',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'testing' => 'Testing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const PRIORITY_MEDIUM = 'medium';

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'start_date' => 'date',
            'deadline' => 'date',
            'completed_at' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            $project->project_number = $project->project_number
                ?: app(DocumentNumberGenerator::class)->next('projects', 'project_number', 'project_prefix', 'PRJ');
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'project_manager_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class)->withPivot('role');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order')->orderBy('id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ProjectRequirement::class);
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class);
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    public function timesheets(): HasMany
    {
        return $this->hasMany(Timesheet::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['planning', 'active', 'on_hold', 'testing']);
    }

    public function isOverdue(): bool
    {
        return $this->deadline
            && $this->deadline->isPast()
            && ! in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_CANCELLED], true);
    }

    /** Progress recomputed from completed tasks, used when tasks drive delivery. */
    public function taskProgress(): int
    {
        $total = $this->tasks()->count();

        if ($total === 0) {
            return (int) $this->progress;
        }

        $done = $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();

        return (int) round($done / $total * 100);
    }

    public function activityLabel(): string
    {
        return $this->project_number.' — '.$this->name;
    }
}
