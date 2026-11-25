<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * One customer's access to one SaaS product. Access is live while the status
 * is trial/active and expires_at is in the future; the moment expires_at
 * passes the credentials are withheld and the customer is asked to pay.
 */
#[Fillable([
    'user_id', 'saas_product_id', 'status', 'license_key', 'starts_at', 'expires_at',
    'access_url', 'access_username', 'access_password',
    'payment_status', 'payment_method', 'payment_reference', 'payment_amount',
    'payment_receipt', 'payment_note', 'payment_submitted_at', 'notes',
])]
#[Hidden(['access_password'])]
class SaasSubscription extends Model
{
    use LogsActivity;

    public const STATUSES = [
        'trial' => 'Free Trial',
        'active' => 'Active (Paid)',
        'expired' => 'Expired',
        'cancelled' => 'Cancelled',
    ];

    public const PAYMENT_STATUSES = [
        'pending' => 'Pending Review',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'payment_submitted_at' => 'datetime',
            'payment_amount' => 'decimal:2',
            'access_password' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (SaasSubscription $subscription) {
            $subscription->license_key ??= static::newLicenseKey();
            $subscription->status ??= 'trial';
            $subscription->starts_at ??= now();

            // The trial window starts the moment access is created.
            if (! $subscription->expires_at) {
                $days = $subscription->product?->trialLength() ?? 7;
                $subscription->expires_at = Carbon::parse($subscription->starts_at)->addDays($days);
            }
        });

        // Moving the expiry into the future on an expired record re-opens it.
        static::saving(function (SaasSubscription $subscription) {
            if ($subscription->status === 'expired' && $subscription->expires_at?->isFuture() && $subscription->isDirty('expires_at')) {
                $subscription->status = $subscription->payment_status === 'approved' ? 'active' : 'trial';
            }
        });
    }

    public static function newLicenseKey(): string
    {
        do {
            $key = strtoupper(Str::random(32));
        } while (static::where('license_key', $key)->exists());

        return $key;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(SaasProduct::class, 'saas_product_id')->withTrashed();
    }

    /** Subscriptions whose access window has closed but are not flagged yet. */
    public function scopeLapsed(Builder $query): Builder
    {
        return $query->whereIn('status', ['trial', 'active'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function hasAccess(): bool
    {
        return in_array($this->status, ['trial', 'active'], true)
            && $this->expires_at !== null
            && $this->expires_at->isFuture();
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /** Flags the record expired once its window has passed. Returns true when it changed. */
    public function syncExpiry(): bool
    {
        if (in_array($this->status, ['trial', 'active'], true) && $this->expires_at && ! $this->expires_at->isFuture()) {
            $this->status = 'expired';
            $this->saveQuietly();

            return true;
        }

        return false;
    }

    public function awaitingPayment(): bool
    {
        return $this->payment_status === 'pending';
    }

    /** Where the customer is sent when they open the software. */
    public function launchUrl(): ?string
    {
        return $this->access_url ?: $this->product?->app_url;
    }

    /** Extends access by one billing period after a payment is approved. */
    public function approvePayment(): void
    {
        $from = $this->expires_at && $this->expires_at->isFuture() ? $this->expires_at : now();

        $this->forceFill([
            'status' => 'active',
            'payment_status' => 'approved',
            'expires_at' => $from->copy()->addDays($this->product?->billingLength() ?? 30),
        ])->save();
    }

    public function activityLabel(): string
    {
        return trim(($this->product?->name ?? 'SaaS').' — '.($this->user?->email ?? 'customer #'.$this->user_id));
    }

    public function stateLabel(): string
    {
        if ($this->status === 'cancelled') {
            return 'Cancelled';
        }

        if (! $this->hasAccess()) {
            return $this->awaitingPayment() ? 'Expired — payment under review' : 'Expired';
        }

        return $this->isTrial() ? 'Free Trial' : 'Active';
    }
}
