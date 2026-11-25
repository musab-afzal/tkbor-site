<?php

namespace App\Notifications;

use App\Models\Invoice;

class InvoiceOverdue extends AdminNotification
{
    public function __construct(protected Invoice $invoice) {}

    protected function payload(): array
    {
        return [
            'title' => 'Invoice '.$this->invoice->invoice_number.' is overdue',
            'message' => $this->invoice->client?->displayName().' — '.money($this->invoice->balance, $this->invoice->currency).' outstanding',
            'icon' => 'fa-file-invoice-dollar',
            'url' => route('admin.invoices.show', $this->invoice),
        ];
    }
}
