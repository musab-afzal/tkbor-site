<?php

namespace App\Models;

use App\Services\LineItemTotals;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'invoice_id', 'pricing_item_id', 'name', 'description',
    'quantity', 'unit_price', 'sort_order',
])]
class InvoiceItem extends Model
{
    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        $derive = function (InvoiceItem $item) {
            $item->line_total = LineItemTotals::lineTotal((float) $item->quantity, (float) $item->unit_price);
        };

        static::creating($derive);
        static::updating($derive);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function pricingItem(): BelongsTo
    {
        return $this->belongsTo(PricingItem::class);
    }
}
