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
    'expense_category_id', 'project_id', 'employee_id', 'vendor', 'title',
    'description', 'amount', 'currency', 'expense_date', 'method', 'receipt', 'status',
])]
class Expense extends Model
{
    use LogsActivity, SoftDeletes, TracksBlame;

    public const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
    ];

    public const METHODS = [
        'bank_transfer' => 'Bank Transfer',
        'cash' => 'Cash',
        'cheque' => 'Cheque',
        'card' => 'Card',
        'paypal' => 'PayPal',
        'other' => 'Other',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'expense_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::creating(function (Expense $expense) {
            $expense->expense_number = $expense->expense_number
                ?: app(DocumentNumberGenerator::class)->next('expenses', 'expense_number', 'expense_prefix', 'EXP');
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function activityLabel(): string
    {
        return $this->expense_number.' — '.$this->title;
    }
}
