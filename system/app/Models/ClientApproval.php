<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Anything sent to a client for sign-off: a design, a page of content, an SRS
 * item or a change request. The optional morph link points at the source record.
 */
#[Fillable([
    'client_id', 'project_id', 'approvable_type', 'approvable_id', 'item_type',
    'title', 'description', 'sent_date', 'responded_date', 'status', 'client_remarks',
])]
class ClientApproval extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'revision_requested' => 'Revision Requested',
    ];

    public const ITEM_TYPES = [
        'design' => 'Design',
        'content' => 'Content',
        'change_request' => 'Change Request',
        'requirement' => 'Requirement',
        'milestone' => 'Milestone',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return ['sent_date' => 'date', 'responded_date' => 'date'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function approvable(): MorphTo
    {
        return $this->morphTo();
    }
}
