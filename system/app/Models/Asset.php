<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Domains, hosting, SSL, licences and subscriptions share one renewal-tracking
 * shape, so they share one table and are filtered by type.
 */
#[Fillable([
    'type', 'name', 'client_id', 'project_id', 'provider', 'provider_url',
    'account_reference', 'purchase_date', 'start_date', 'expiry_date', 'cost',
    'currency', 'billing_cycle', 'auto_renew', 'reminder_days', 'status',
    'credentials_note', 'notes',
])]
class Asset extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const TYPES = [
        'domain' => 'Domain',
        'hosting' => 'Hosting',
        'ssl' => 'SSL Certificate',
        'license' => 'License',
        'subscription' => 'Subscription',
    ];

    public const BILLING_CYCLES = [
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly' => 'Yearly',
        'biennial' => 'Every 2 Years',
        'one_time' => 'One Time',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
        'transferred' => 'Transferred',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'start_date' => 'date',
            'expiry_date' => 'date',
            'cost' => 'decimal:2',
            'auto_renew' => 'boolean',
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

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeExpiringWithin(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [today(), today()->addDays($days)]);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today());
    }

    public function daysUntilExpiry(): ?int
    {
        return $this->expiry_date ? (int) today()->diffInDays($this->expiry_date, false) : null;
    }

    public function isExpiringSoon(): bool
    {
        $days = $this->daysUntilExpiry();

        return $days !== null && $days >= 0 && $days <= (int) $this->reminder_days;
    }

    public function isExpired(): bool
    {
        $days = $this->daysUntilExpiry();

        return $days !== null && $days < 0;
    }
}
