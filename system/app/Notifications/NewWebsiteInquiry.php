<?php

namespace App\Notifications;

use App\Models\WebsiteInquiry;

class NewWebsiteInquiry extends AdminNotification
{
    public function __construct(protected WebsiteInquiry $inquiry) {}

    protected function payload(): array
    {
        return [
            'title' => 'New website inquiry from '.$this->inquiry->name,
            'message' => $this->inquiry->service ?: $this->inquiry->subject ?: 'General enquiry',
            'icon' => 'fa-inbox',
            'url' => route('admin.inquiries.show', $this->inquiry),
        ];
    }
}
