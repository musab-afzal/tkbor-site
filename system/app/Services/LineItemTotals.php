<?php

namespace App\Services;

/**
 * Shared money maths for any document made of priced line items. Totals are
 * always derived from the stored items, never trusted from form input, so a
 * cached column can never drift away from its rows.
 */
class LineItemTotals
{
    public static function lineTotal(float $quantity, float $unitPrice, bool $isFree = false): float
    {
        return $isFree ? 0.0 : round($quantity * $unitPrice, 2);
    }

    public static function discountAmount(float $subtotal, string $type, float $value): float
    {
        $amount = $type === 'percentage'
            ? $subtotal * (min(max($value, 0), 100) / 100)
            : max($value, 0);

        return round(min($amount, $subtotal), 2);
    }

    public static function taxAmount(float $taxableBase, float $percentage): float
    {
        return round($taxableBase * (max($percentage, 0) / 100), 2);
    }
}
