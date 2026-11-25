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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'client_id', 'project_id', 'subject', 'description', 'category', 'priority',
    'status', 'assigned_to', 'contact_name', 'contact_email', 'resolution',
])]
class Ticket extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'waiting_client' => 'Waiting on Client',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public const CATEGORIES = [
        'general' => 'General',
        'bug' => 'Bug',
        'feature_request' => 'Feature Request',
        'hosting' => 'Hosting',
        'email' => 'Email',
        'billing' => 'Billing',
        'content' => 'Content Update',
        'security' => 'Security',
    ];

    protected function casts(): array
    {
        return [
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ticket_number = $ticket->ticket_number
                ?: app(DocumentNumberGenerator::class)->next('tickets', 'ticket_number', 'ticket_prefix', 'TKT');
        });

        static::saving(function (Ticket $ticket) {
            if (in_array($ticket->status, ['resolved', 'closed'], true)) {
                $ticket->resolved_at = $ticket->resolved_at ?: now();
            } elseif ($ticket->isDirty('status')) {
                $ticket->resolved_at = null;
            }
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

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->oldest('id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    public function activityLabel(): string
    {
        return $this->ticket_number.' — '.$this->subject;
    }
}
