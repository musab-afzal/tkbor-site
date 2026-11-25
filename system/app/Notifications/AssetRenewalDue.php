<?php

namespace App\Notifications;

use App\Models\Asset;

class AssetRenewalDue extends AdminNotification
{
    public function __construct(protected Asset $asset) {}

    protected function payload(): array
    {
        $days = $this->asset->daysUntilExpiry();

        return [
            'title' => Asset::TYPES[$this->asset->type].' renewal: '.$this->asset->name,
            'message' => $days !== null && $days < 0
                ? 'Expired '.abs($days).' days ago'
                : 'Expires in '.$days.' days',
            'icon' => 'fa-server',
            'url' => route('admin.assets.index'),
        ];
    }
}
