<?php

namespace App\Notifications;

use App\Models\SaasSubscription;

/** Tells staff a customer started a trial or submitted a payment. */
class SaasActivity extends AdminNotification
{
    public function __construct(protected SaasSubscription $subscription, protected string $event) {}

    protected function payload(): array
    {
        $customer = $this->subscription->user?->name ?? 'A customer';
        $product = $this->subscription->product?->name ?? 'software';

        return match ($this->event) {
            'payment' => [
                'title' => 'SaaS payment submitted by '.$customer,
                'message' => $product.' — ref '.($this->subscription->payment_reference ?: 'n/a').'. Review and approve to extend access.',
                'icon' => 'fa-money-bill-wave',
                'url' => route('admin.saas-subscriptions.index', ['payment_status' => 'pending']),
            ],
            default => [
                'title' => $customer.' started a free trial',
                'message' => $product.' trial until '.$this->subscription->expires_at?->format('M d, Y').'. Add their login details.',
                'icon' => 'fa-cloud',
                'url' => route('admin.saas-subscriptions.edit', $this->subscription),
            ],
        };
    }
}
