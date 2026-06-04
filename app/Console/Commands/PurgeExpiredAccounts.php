<?php

namespace App\Console\Commands;

use App\Models\LiveChatGroup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PurgeExpiredAccounts extends Command
{
    protected $signature = 'account:purge-expired {--dry-run : Preview users to purge without deleting}';

    protected $description = 'Permanently delete soft-deleted users whose 30-day window has expired';

    public function handle(): int
    {
        $this->info('Scanning for expired soft-deleted accounts...');

        $cutoff = now()->subDays(30);
        $users = User::onlyTrashed()->where('deleted_at', '<', $cutoff)->get();

        if ($users->isEmpty()) {
            $this->info('No expired accounts found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$users->count()} expired account(s).");

        if ($this->option('dry-run')) {
            $this->warn('Dry-run mode: no data will be modified.');
            foreach ($users as $user) {
                $this->line("  [{$user->id}] {$user->email} — deleted at {$user->deleted_at}");
            }
            return Command::SUCCESS;
        }

        $purged = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                DB::beginTransaction();

                $userId = $user->id;

                // 1. Clean up polymorphic tables (no FK cascade)
                DB::table('notifications')
                    ->where('notifiable_type', User::class)
                    ->where('notifiable_id', $userId)
                    ->delete();

                DB::table('personal_access_tokens')
                    ->where('tokenable_type', User::class)
                    ->where('tokenable_id', $userId)
                    ->delete();

                DB::table('activity_log')
                    ->where('causer_type', User::class)
                    ->where('causer_id', $userId)
                    ->delete();

                DB::table('model_has_roles')
                    ->where('model_type', User::class)
                    ->where('model_id', $userId)
                    ->delete();

                DB::table('model_has_permissions')
                    ->where('model_type', User::class)
                    ->where('model_id', $userId)
                    ->delete();

                // 2. Handle LiveChatGroups owned by this user
                LiveChatGroup::where('created_by', $userId)->each(function ($group) use ($userId) {
                    if ($group->participants()->where('user_id', '!=', $userId)->exists()) {
                        $group->created_by = $group->participants()
                            ->where('user_id', '!=', $userId)
                            ->inRandomOrder()
                            ->first()
                            ->user_id;
                        $group->save();
                    } else {
                        $group->delete();
                    }
                });

                // 3. Clean up Spatie Media Library
                $user->clearMediaCollection('profile_photo');

                // 4. Clean up profile photo file from storage
                if ($user->profile_photo_path && !filter_var($user->profile_photo_path, FILTER_VALIDATE_URL)) {
                    $disk = config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret') ? 's3' : 'public';
                    \Illuminate\Support\Facades\Storage::disk($disk)->delete($user->profile_photo_path);
                }

                // 5. Force delete (cascade handles the rest)
                $user->forceDelete();

                DB::commit();
                $purged++;
                $this->line("  Purged: [{$userId}] {$user->email}");

            } catch (Throwable $e) {
                DB::rollBack();
                $failed++;
                Log::error("Failed to purge user {$user->id}: " . $e->getMessage());
                $this->error("  Failed: [{$user->id}] {$user->email} — {$e->getMessage()}");
            }
        }

        $this->info("Done. Purged: {$purged}, Failed: {$failed}");
        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
