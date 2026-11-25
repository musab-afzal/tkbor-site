<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'invoice_id', 'client_id', 'amount', 'currency', 'payment_date', 'method',
    'reference', 'notes', 'receipt', 'status',
])]
class Payment extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        'pending' => 'Pending',
        'completed' => 'Completed',
        'failed' => 'Failed',
        'refunded' => 'Refunded',
    ];

    public const METHODS = [
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'card' => 'Card',
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'wise' => 'Wise',
        'crypto' => 'Crypto',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Payment $payment) {
            $payment->payment_number = $payment->payment_number
                ?: app(DocumentNumberGenerator::class)->next('payments', 'payment_number', 'payment_prefix', 'PAY');
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function activityLabel(): string
    {
        return $this->payment_number;
    }
}
