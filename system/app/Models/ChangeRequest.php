<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'project_id', 'title', 'requested_by', 'description', 'reason', 'cost_impact',
    'timeline_impact_days', 'status', 'client_approved', 'client_approved_at', 'remarks',
])]
class ChangeRequest extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'pending' => 'Pending',
        'under_review' => 'Under Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'implemented' => 'Implemented',
    ];

    protected function casts(): array
    {
        return [
            'cost_impact' => 'decimal:2',
            'timeline_impact_days' => 'integer',
            'client_approved' => 'boolean',
            'client_approved_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ChangeRequest $request) {
            $request->request_number = $request->request_number
                ?: app(DocumentNumberGenerator::class)->next('change_requests', 'request_number', 'change_request_prefix', 'CR');
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function activityLabel(): string
    {
        return $this->request_number.' — '.$this->title;
    }
}
