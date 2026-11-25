<?php

namespace App\Notifications;

use App\Models\Lead;

class NewLeadCreated extends AdminNotification
{
    public function __construct(protected Lead $lead) {}

    protected function payload(): array
    {
        return [
            'title' => 'New lead: '.$this->lead->name,
            'message' => $this->lead->company ?: $this->lead->interested_service ?: 'No company given',
            'icon' => 'fa-bullseye',
            'url' => route('admin.leads.show', $this->lead),
        ];
    }
}
