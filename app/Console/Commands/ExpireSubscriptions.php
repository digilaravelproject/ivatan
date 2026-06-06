<?php

namespace App\Console\Commands;

use App\Services\Subscription\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';
    protected $description = 'Expire subscriptions past their end date';

    public function handle(SubscriptionService $subscriptionService): int
    {
        $this->info('Expiring past-due subscriptions...');

        $expired = $subscriptionService->expirePastDue();

        $this->info("Expired {$expired} subscriptions.");
        Log::info("ExpireSubscriptions: {$expired} subscriptions expired via scheduler");

        return Command::SUCCESS;
    }
}
