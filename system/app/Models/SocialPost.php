<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'title', 'client_id', 'campaign_id', 'platform', 'content', 'media', 'link',
    'scheduled_at', 'published_at', 'status', 'assigned_to', 'notes',
])]
class SocialPost extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'draft' => 'Draft',
        'approved' => 'Approved',
        'scheduled' => 'Scheduled',
        'published' => 'Published',
        'cancelled' => 'Cancelled',
    ];

    public const PLATFORMS = [
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'x' => 'X (Twitter)',
        'tiktok' => 'TikTok',
        'youtube' => 'YouTube',
        'pinterest' => 'Pinterest',
        'threads' => 'Threads',
    ];

    protected function casts(): array
    {
        return ['scheduled_at' => 'datetime', 'published_at' => 'datetime'];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }
}
