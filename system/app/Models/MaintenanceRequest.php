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
    'client_id', 'project_id', 'title', 'description', 'priority', 'estimated_cost',
    'approved_cost', 'estimated_hours', 'status', 'client_approved',
    'client_approved_at', 'assigned_to', 'notes',
])]
class MaintenanceRequest extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'pending' => 'Pending',
        'quoted' => 'Quoted',
        'approved' => 'Approved',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
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
            'estimated_cost' => 'decimal:2',
            'approved_cost' => 'decimal:2',
            'client_approved' => 'boolean',
            'client_approved_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MaintenanceRequest $request) {
            $request->request_number = $request->request_number
                ?: app(DocumentNumberGenerator::class)->next('maintenance_requests', 'request_number', 'maintenance_prefix', 'MR');
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function activityLabel(): string
    {
        return $this->request_number.' — '.$this->title;
    }
}
