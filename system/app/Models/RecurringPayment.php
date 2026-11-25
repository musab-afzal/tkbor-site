<?php

namespace App\Models;

use App\Models\Concerns\LogsActivity;
use App\Models\Concerns\TracksBlame;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Recurring agency income and outgoings: hosting, licences, subscriptions,
 * salaries, retainers.
 */
#[Fillable([
    'title', 'direction', 'category', 'client_id', 'expense_category_id', 'vendor',
    'amount', 'currency', 'frequency', 'start_date', 'end_date', 'next_due_date',
    'reminder_days', 'auto_renew', 'notes', 'status',
])]
class RecurringPayment extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const DIRECTIONS = [
        'expense' => 'Expense',
        'income' => 'Income',
    ];

    public const FREQUENCIES = [
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly' => 'Yearly',
    ];

    public const STATUSES = [
        'active' => 'Active',
        'paused' => 'Paused',
        'cancelled' => 'Cancelled',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'next_due_date' => 'date',
            'last_generated_at' => 'date',
            'auto_renew' => 'boolean',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function scopeDueSoon(Builder $query, int $days = 30): Builder
    {
        return $query->where('status', 'active')
            ->whereNotNull('next_due_date')
            ->whereDate('next_due_date', '<=', today()->addDays($days));
    }

    public function isOverdue(): bool
    {
        return $this->status === 'active'
            && $this->next_due_date
            && $this->next_due_date->isPast();
    }

    /** Roll next_due_date forward by one period. */
    public function advanceDueDate(): void
    {
        if (! $this->next_due_date) {
            return;
        }

        $this->update([
            'last_generated_at' => $this->next_due_date,
            'next_due_date' => $this->addPeriod($this->next_due_date),
        ]);
    }

    protected function addPeriod(Carbon $date): Carbon
    {
        return match ($this->frequency) {
            'weekly' => $date->copy()->addWeek(),
            'quarterly' => $date->copy()->addMonths(3),
            'half_yearly' => $date->copy()->addMonths(6),
            'yearly' => $date->copy()->addYear(),
            default => $date->copy()->addMonth(),
        };
    }
}
