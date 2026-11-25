<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Every public contact / quote form on the website lands here. This is also the
 * CMS "Contact Messages" screen — there is no second table for them.
 */
#[Fillable([
    'name', 'company', 'email', 'phone', 'whatsapp', 'service', 'budget', 'subject',
    'message', 'source_page', 'ip_address', 'user_agent', 'status', 'priority',
    'assigned_to', 'admin_notes',
])]
class WebsiteInquiry extends Model
{
    use LogsActivity, SoftDeletes;

    public const STATUS_NEW = 'new';

    public const STATUS_READ = 'read';

    public const STATUS_CONVERTED = 'converted';

    public const STATUS_SPAM = 'spam';

    public const STATUSES = [
        'new' => 'New',
        'read' => 'Read',
        'contacted' => 'Contacted',
        'qualified' => 'Qualified',
        'converted' => 'Converted',
        'closed' => 'Closed',
        'spam' => 'Spam',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (WebsiteInquiry $inquiry) {
            $inquiry->inquiry_number = $inquiry->inquiry_number
                ?: app(DocumentNumberGenerator::class)->next('website_inquiries', 'inquiry_number', 'inquiry_prefix', 'INQ');
        });
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function convertedLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'converted_lead_id');
    }

    public function convertedClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    public function convertedQuotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class, 'converted_quotation_id');
    }

    public function convertedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'converted_project_id');
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function markAsRead(): void
    {
        if ($this->read_at) {
            return;
        }

        $this->update([
            'read_at' => now(),
            'status' => $this->status === self::STATUS_NEW ? self::STATUS_READ : $this->status,
        ]);
    }

    public function activityLabel(): string
    {
        return $this->inquiry_number.' — '.$this->name;
    }
}
