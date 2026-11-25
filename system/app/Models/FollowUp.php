<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'lead_id', 'client_id', 'website_inquiry_id', 'assigned_to', 'scheduled_at',
    'type', 'notes', 'result', 'status', 'completed_at', 'next_follow_up_at',
])]
class FollowUp extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const TYPES = [
        'call' => 'Call',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'meeting' => 'Meeting',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'next_follow_up_at' => 'datetime',
            'reminder_sent' => 'boolean',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function websiteInquiry(): BelongsTo
    {
        return $this->belongsTo(WebsiteInquiry::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->pending()->where('scheduled_at', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query->pending()->whereDate('scheduled_at', today());
    }

    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_PENDING
            && $this->scheduled_at
            && $this->scheduled_at->isPast();
    }

    /** Whichever party this follow-up belongs to. */
    public function subjectName(): string
    {
        return $this->lead?->name
            ?? $this->client?->displayName()
            ?? $this->websiteInquiry?->name
            ?? 'General';
    }

    public function activityLabel(): string
    {
        return 'Follow-up with '.$this->subjectName();
    }
}
