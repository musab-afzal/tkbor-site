<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'client_id', 'project_id', 'platform', 'objective', 'budget', 'spent',
    'currency', 'start_date', 'end_date', 'impressions', 'clicks', 'conversions',
    'results', 'notes', 'status', 'managed_by',
])]
class Campaign extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const PLATFORMS = [
        'google_ads' => 'Google Ads',
        'meta_ads' => 'Meta Ads',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'tiktok' => 'TikTok',
        'youtube' => 'YouTube',
        'x' => 'X (Twitter)',
        'email' => 'Email',
        'seo' => 'SEO',
        'other' => 'Other',
    ];

    public const STATUSES = [
        'planned' => 'Planned',
        'active' => 'Active',
        'paused' => 'Paused',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const OBJECTIVES = [
        'awareness' => 'Brand Awareness',
        'traffic' => 'Traffic',
        'leads' => 'Lead Generation',
        'sales' => 'Sales',
        'engagement' => 'Engagement',
        'app_installs' => 'App Installs',
    ];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'spent' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'managed_by');
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function clickThroughRate(): ?float
    {
        return $this->impressions > 0
            ? round($this->clicks / $this->impressions * 100, 2)
            : null;
    }

    public function costPerConversion(): ?float
    {
        return $this->conversions > 0
            ? round((float) $this->spent / $this->conversions, 2)
            : null;
    }
}
