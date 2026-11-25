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
    'client_id', 'lead_id', 'website_inquiry_id', 'project_name', 'project_type',
    'quotation_date', 'valid_until', 'currency', 'project_summary', 'requirements',
    'scope', 'proposed_solution', 'technologies', 'project_timeline', 'deliverables',
    'revisions_included', 'support_period', 'hosting_domain_info',
    'third_party_charges', 'client_responsibilities', 'terms_conditions',
    'internal_notes', 'discount_type', 'discount_value', 'tax_percentage',
    'status', 'prepared_by', 'client_accepted_by', 'client_accepted_date',
    'client_remarks',
])]
class Quotation extends Model
{
    use HasAttachments, LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_EXPIRED = 'expired';

    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
    ];

    public const SECTION_MAIN = 'main';

    public const SECTION_ADDITIONAL = 'additional';

    public const SECTION_FREE = 'free';

    public const SECTIONS = [
        'main' => 'Modules & Services',
        'additional' => 'Additional Services',
        'free' => 'Free Services',
    ];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'quotation_date' => 'date',
            'valid_until' => 'date',
            'client_accepted_date' => 'date',
            'sent_at' => 'datetime',
            'decided_at' => 'datetime',
            'discount_value' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'grand_total' => 'decimal:2',
            'free_services_value' => 'decimal:2',
            'optional_items_value' => 'decimal:2',
            'internal_cost_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            $quotation->quotation_number = $quotation->quotation_number
                ?: app(DocumentNumberGenerator::class)->next('quotations', 'quotation_number', 'quotation_prefix', 'QT');
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function websiteInquiry(): BelongsTo
    {
        return $this->belongsTo(WebsiteInquiry::class);
    }

    public function preparedBy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'prepared_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(QuotationMilestone::class)->orderBy('sort_order')->orderBy('id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function convertedProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'converted_project_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function itemsInSection(string $section)
    {
        return $this->items->where('section', $section);
    }

    /** The number without any -R suffix, used to build the next revision. */
    public function baseNumber(): string
    {
        return preg_replace('/-R\d+$/', '', (string) $this->quotation_number);
    }

    public function isExpired(): bool
    {
        return $this->valid_until
            && $this->valid_until->isPast()
            && ! in_array($this->status, [self::STATUS_APPROVED, self::STATUS_REJECTED], true);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_SENT], true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_DRAFT, self::STATUS_SENT]);
    }

    public function activityLabel(): string
    {
        return $this->quotation_number.' — '.$this->project_name;
    }
}
