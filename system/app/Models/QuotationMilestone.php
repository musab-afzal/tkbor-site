<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'quotation_id', 'title', 'description', 'percentage', 'amount',
    'due_condition', 'sort_order',
])]
class QuotationMilestone extends Model
{
    protected function casts(): array
    {
        return ['percentage' => 'decimal:2', 'amount' => 'decimal:2'];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }
}
