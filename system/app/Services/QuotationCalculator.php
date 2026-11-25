<?php

namespace App\Services;

use App\Models\Quotation;

class QuotationCalculator
{
    /**
     * Recalculate every cached money column from the quotation's own items and
     * persist the result. Safe to call as often as you like.
     */
    public function recalculate(Quotation $quotation): Quotation
    {
        $items = $quotation->items()->get();

        $subtotal = 0.0;
        $optional = 0.0;
        $freeValue = 0.0;
        $internalCost = 0.0;
        $days = 0;

        foreach ($items as $item) {
            $gross = round((float) $item->quantity * (float) $item->unit_price, 2);
            $internalCost += round((float) $item->quantity * (float) ($item->internal_cost ?? 0), 2);

            if ($item->is_free) {
                // Free services still carry a headline value shown to the client.
                $freeValue += $gross;

                continue;
            }

            if ($item->is_optional) {
                $optional += $gross;

                continue;
            }

            $subtotal += (float) $item->line_total;
            $days += (int) $item->estimated_days;
        }

        $subtotal = round($subtotal, 2);
        $discount = LineItemTotals::discountAmount($subtotal, $quotation->discount_type ?? 'percentage', (float) $quotation->discount_value);
        $tax = LineItemTotals::taxAmount($subtotal - $discount, (float) $quotation->tax_percentage);

        $quotation->forceFill([
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'grand_total' => round($subtotal - $discount + $tax, 2),
            'free_services_value' => round($freeValue, 2),
            'optional_items_value' => round($optional, 2),
            'internal_cost_total' => round($internalCost, 2),
            'total_estimated_days' => $days,
        ])->save();

        return $quotation;
    }

    /**
     * Payment milestones stored as percentages are re-costed against the current
     * grand total so they always add up to the quoted price.
     */
    public function syncMilestoneAmounts(Quotation $quotation): void
    {
        foreach ($quotation->milestones as $milestone) {
            if ($milestone->percentage === null) {
                continue;
            }

            $milestone->update([
                'amount' => round((float) $quotation->grand_total * ((float) $milestone->percentage / 100), 2),
            ]);
        }
    }
}
