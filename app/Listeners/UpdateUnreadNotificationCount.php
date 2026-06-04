<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UpdateUnreadNotificationCount implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event): void
    {
        // Only handle database notifications
        if ($event->channel !== 'database') {
            return;
        }

        $notifiable = $event->notifiable;

        $hasMethod = method_exists($notifiable, 'routeNotificationForDatabase');

        if (! $hasMethod) {
            return;
        }

        // Only proceed if it's a User
        if (! $notifiable instanceof \App\Models\User) {
            return;
        }

        $userId = $notifiable->getKey();

        DB::transaction(function () use ($userId) {
            $updated = DB::table('notification_unread_counts')
                ->where('user_id', $userId)
                ->increment('unread_count');

            if (! $updated) {
                DB::table('notification_unread_counts')->insert([
                    'user_id' => $userId,
                    'unread_count' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });
    }
}
