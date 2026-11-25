<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'code', 'title', 'requirement_type', 'description', 'priority',
    'status', 'client_approved', 'client_approved_at', 'version', 'change_log',
])]
class ProjectRequirement extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const TYPES = [
        'functional' => 'Functional',
        'non_functional' => 'Non-Functional',
    ];

    public const STATUSES = [
        'draft' => 'Draft',
        'review' => 'In Review',
        'approved' => 'Approved',
        'implemented' => 'Implemented',
        'rejected' => 'Rejected',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    protected function casts(): array
    {
        return [
            'client_approved' => 'boolean',
            'client_approved_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
