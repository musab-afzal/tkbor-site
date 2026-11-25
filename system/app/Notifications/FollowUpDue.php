<?php

namespace App\Notifications;

use App\Models\FollowUp;

class FollowUpDue extends AdminNotification
{
    public function __construct(protected FollowUp $followUp) {}

    protected function payload(): array
    {
        return [
            'title' => 'Follow-up due with '.$this->followUp->subjectName(),
            'message' => $this->followUp->scheduled_at?->format('M d, Y g:i A') ?? '',
            'icon' => 'fa-phone-volume',
            'url' => route('admin.follow-ups.index'),
        ];
    }
}
