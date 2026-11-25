<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\HasComments;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'title', 'description', 'steps_to_reproduce', 'severity',
    'priority', 'assigned_to', 'status', 'resolution', 'reported_date', 'resolved_date',
])]
class Bug extends Model
{
    use HasAttachments, HasComments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'testing' => 'Testing',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'reopened' => 'Reopened',
        'wont_fix' => 'Will Not Fix',
    ];

    public const SEVERITIES = [
        'trivial' => 'Trivial',
        'minor' => 'Minor',
        'major' => 'Major',
        'critical' => 'Critical',
        'blocker' => 'Blocker',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected function casts(): array
    {
        return ['reported_date' => 'date', 'resolved_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Bug $bug) {
            $bug->bug_number = $bug->bug_number
                ?: app(DocumentNumberGenerator::class)->next('bugs', 'bug_number', 'bug_prefix', 'BUG');
            $bug->reported_date = $bug->reported_date ?: now()->toDateString();
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['resolved', 'closed', 'wont_fix']);
    }

    public function activityLabel(): string
    {
        return $this->bug_number.' — '.$this->title;
    }
}
