<?php

namespace App\Notifications;

use App\Models\Quotation;

class QuotationDecided extends AdminNotification
{
    public function __construct(protected Quotation $quotation) {}

    protected function payload(): array
    {
        $approved = $this->quotation->status === Quotation::STATUS_APPROVED;

        return [
            'title' => 'Quotation '.$this->quotation->quotation_number.' was '.($approved ? 'approved' : 'rejected'),
            'message' => $this->quotation->client?->displayName().' — '.money($this->quotation->grand_total, $this->quotation->currency),
            'icon' => $approved ? 'fa-file-circle-check' : 'fa-file-circle-xmark',
            'url' => route('admin.quotations.show', $this->quotation),
        ];
    }
}
