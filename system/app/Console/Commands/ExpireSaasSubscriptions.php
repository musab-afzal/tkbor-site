<?php

namespace App\Console\Commands;

use App\Models\SaasSubscription;
use Illuminate\Console\Command;

/**
 * Flags trials and paid periods whose end date has passed. Access is also
 * checked live on every request, so this only keeps the admin listing and
 * reports tidy when nobody has looked at a record.
 */
class ExpireSaasSubscriptions extends Command
{
    protected $signature = 'saas:expire';

    protected $description = 'Mark SaaS trials and subscriptions whose access window has ended as expired';

    public function handle(): int
    {
        $count = SaasSubscription::lapsed()->update(['status' => 'expired', 'updated_at' => now()]);

        $this->info($count.' SaaS subscription(s) expired.');

        return self::SUCCESS;
    }
}
