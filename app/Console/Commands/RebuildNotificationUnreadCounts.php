<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use Throwable;

class RebuildNotificationUnreadCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:rebuild-unread-counts {--dry-run : Preview changes without modifying the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the notification_unread_counts table from unread notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting rebuild of unread notification counts...');

        try {
            // Step 1: Fetch unread notification counts per user
            $this->info('Fetching unread notifications...');
            $rows = DB::table('notifications')
                ->select('notifiable_id', DB::raw('COUNT(*) as unread'))
                ->whereNull('read_at')
                ->where('notifiable_type', User::class)
                ->groupBy('notifiable_id')
                ->get();

            // Step 2: Prepare insert data
            $now = now();
            $inserts = $rows->map(fn($row) => [
                'user_id'      => $row->notifiable_id,
                'unread_count' => $row->unread,
                'created_at'   => $now,
                'updated_at'   => $now,
            ])->toArray();

            if (empty($inserts)) {
                $this->info('No unread notifications found. Nothing to update.');
                return Command::SUCCESS;
            }

            // Step 3: If dry-run, show what would happen and exit
            if ($this->option('dry-run')) {
                $this->warn('Dry run mode enabled — no data will be written.');
                foreach ($inserts as $insert) {
                    $this->line("User ID: {$insert['user_id']} | Unread: {$insert['unread_count']}");
                }
                return Command::SUCCESS;
            }

            // Step 4: Run transaction to truncate and insert new counts
            DB::transaction(function () use ($inserts) {
                // Disable FK checks to safely truncate
                DB::statement('SET FOREIGN_KEY_CHECKS=0');
                DB::table('notification_unread_counts')->truncate();
                DB::statement('SET FOREIGN_KEY_CHECKS=1');

                // Insert new counts
                DB::table('notification_unread_counts')->insert($inserts);
            });

            $this->info('Unread notification counts rebuilt successfully.');
            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            \Log::error('Error rebuilding unread notification counts', ['exception' => $e]);
            return Command::FAILURE;
        }
    }
}
 