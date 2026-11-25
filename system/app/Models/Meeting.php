<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'lead_id', 'client_id', 'scheduled_at', 'duration_minutes', 'mode',
    'location', 'meeting_url', 'agenda', 'notes', 'outcome', 'next_action', 'status',
])]
class Meeting extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const MODES = [
        'physical' => 'Physical',
        'online' => 'Online',
    ];

    public const STATUSES = [
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'no_show' => 'No Show',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime'];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'meeting_participants')
            ->withPivot('attended');
    }

    public function activityLabel(): string
    {
        return $this->title;
    }
}
