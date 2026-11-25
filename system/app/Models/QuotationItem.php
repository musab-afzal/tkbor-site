<?php

namespace App\Models;

use App\Services\LineItemTotals;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'quotation_id', 'pricing_item_id', 'section', 'name', 'description', 'features',
    'quantity', 'unit_price', 'internal_cost', 'estimated_days', 'is_optional',
    'is_free', 'sort_order',
])]
class QuotationItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'internal_cost' => 'decimal:2',
            'is_optional' => 'boolean',
            'is_free' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // The line total is never accepted from the form — it is always derived.
        $derive = function (QuotationItem $item) {
            $item->line_total = LineItemTotals::lineTotal(
                (float) $item->quantity,
                (float) $item->unit_price,
                (bool) $item->is_free,
            );
        };

        static::creating($derive);
        static::updating($derive);
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function pricingItem(): BelongsTo
    {
        return $this->belongsTo(PricingItem::class);
    }

    /** Headline value even when the item is given away free. */
    public function grossValue(): float
    {
        return round((float) $this->quantity * (float) $this->unit_price, 2);
    }
}
