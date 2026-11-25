<?php

namespace App\Services;

use App\Models\Invoice;

class InvoiceCalculator
{
    /**
     * Rebuild totals from the invoice items and the payments recorded against
     * the invoice, then move the status along if it is payment driven.
     */
    public function recalculate(Invoice $invoice): Invoice
    {
        $subtotal = round((float) $invoice->items()->sum('line_total'), 2);

        $discount = LineItemTotals::discountAmount($subtotal, $invoice->discount_type ?? 'percentage', (float) $invoice->discount_value);
        $tax = LineItemTotals::taxAmount($subtotal - $discount, (float) $invoice->tax_percentage);
        $total = round($subtotal - $discount + $tax, 2);

        $paid = round((float) $invoice->payments()->where('status', 'completed')->sum('amount'), 2);
        $balance = round($total - $paid, 2);

        $invoice->forceFill([
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total' => $total,
            'paid_amount' => $paid,
            'balance' => $balance,
            'status' => $this->resolveStatus($invoice, $total, $paid, $balance),
        ])->save();

        return $invoice;
    }

    protected function resolveStatus(Invoice $invoice, float $total, float $paid, float $balance): string
    {
        // Draft and cancelled are manual states — payments never override them.
        if (in_array($invoice->status, [Invoice::STATUS_DRAFT, Invoice::STATUS_CANCELLED], true)) {
            return $invoice->status;
        }

        if ($total > 0 && $balance <= 0) {
            return Invoice::STATUS_PAID;
        }

        if ($paid > 0) {
            return Invoice::STATUS_PARTIAL;
        }

        if ($invoice->due_date && $invoice->due_date->isPast()) {
            return Invoice::STATUS_OVERDUE;
        }

        return Invoice::STATUS_SENT;
    }
}
