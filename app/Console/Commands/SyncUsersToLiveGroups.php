<?php

namespace App\Console\Commands;

use App\Services\LiveChatGroupService;
use Illuminate\Console\Command;

class SyncUsersToLiveGroups extends Command
{
    protected $signature = 'live-chat:sync-users';
    protected $description = 'Add all existing users to all active live chat groups';

    public function handle(LiveChatGroupService $service): int
    {
        $this->info('Syncing users to live chat groups...');

        $added = $service->addAllExistingUsers();

        $this->info("Done! Added {$added} user-participant records.");

        return Command::SUCCESS;
    }
}
