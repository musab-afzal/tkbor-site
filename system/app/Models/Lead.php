<?php

namespace App\Models;

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
    'name', 'company', 'email', 'phone', 'whatsapp', 'source', 'website_inquiry_id',
    'service_id', 'interested_service', 'estimated_budget', 'currency', 'priority',
    'status', 'assigned_to', 'notes', 'next_follow_up_at', 'city', 'country',
    'lost_reason', 'client_id',
])]
class Lead extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_NEW = 'new';

    public const STATUS_WON = 'won';

    public const STATUS_LOST = 'lost';

    public const STATUSES = [
        'new' => 'New',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'proposal_sent' => 'Proposal Sent',
        'won' => 'Won',
        'lost' => 'Lost',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    public const SOURCE_WEBSITE = 'website';

    public const SOURCES = [
        'website' => 'Website',
        'referral' => 'Referral',
        'social_media' => 'Social Media',
        'email_campaign' => 'Email Campaign',
        'cold_call' => 'Cold Call',
        'marketplace' => 'Marketplace',
        'event' => 'Event',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return [
            'estimated_budget' => 'decimal:2',
            'next_follow_up_at' => 'date',
            'converted_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            $lead->lead_number = $lead->lead_number
                ?: app(DocumentNumberGenerator::class)->next('leads', 'lead_number', 'lead_prefix', 'LD');
        });
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function websiteInquiry(): BelongsTo
    {
        return $this->belongsTo(WebsiteInquiry::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->latest('id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_WON, self::STATUS_LOST]);
    }

    public function isConverted(): bool
    {
        return $this->client_id !== null;
    }

    public function activityLabel(): string
    {
        return $this->lead_number.' — '.$this->name;
    }
}
