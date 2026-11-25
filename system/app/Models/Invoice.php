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
    'client_id', 'project_id', 'quotation_id', 'milestone_id', 'invoice_date',
    'due_date', 'currency', 'discount_type', 'discount_value', 'tax_percentage',
    'status', 'notes', 'terms',
])]
class Invoice extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_PARTIAL = 'partial';

    public const STATUS_PAID = 'paid';

    public const STATUS_OVERDUE = 'overdue';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        'draft' => 'Draft',
        'sent' => 'Sent',
        'partial' => 'Partially Paid',
        'paid' => 'Paid',
        'overdue' => 'Overdue',
        'cancelled' => 'Cancelled',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'sent_at' => 'datetime',
            'discount_value' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            $invoice->invoice_number = $invoice->invoice_number
                ?: app(DocumentNumberGenerator::class)->next('invoices', 'invoice_number', 'invoice_prefix', 'INV');
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

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(ProjectMilestone::class, 'milestone_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest('payment_date');
    }

    public function scopeUnpaid(Builder $query): Builder
    {
        return $query->whereNotIn('status', [self::STATUS_PAID, self::STATUS_CANCELLED, self::STATUS_DRAFT]);
    }

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && (float) $this->balance > 0
            && ! in_array($this->status, [self::STATUS_PAID, self::STATUS_CANCELLED], true);
    }

    public function activityLabel(): string
    {
        return $this->invoice_number;
    }
}
