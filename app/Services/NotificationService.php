<?php

namespace App\Services;

use App\Channels\FcmChannel;
use App\Models\DeviceToken;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    public function sendToUser(User $notifiable, string $category, array $payload = [], ?array $channels = null): void
    {
        if (!$notifiable instanceof User) {
            \Log::warning('Notifiable is not a User instance');
            return;
        }

        // Mute notifications between blocked users
        $actorId = data_get($payload, 'actor_id');
        if ($actorId) {
            $actor = User::find($actorId);
            if ($actor && $notifiable->hasBlockRelationWith($actor)) {
                return;
            }
        }

        $notification = new GenericNotification($category, $payload);

        if ($channels !== null) {
            $notification->viaOverride($channels);
        } else {
            $defaultChannels = config("notifications.categories.{$category}.channels", config('notifications.default_channels', ['database', 'broadcast']));

            $hasDeviceTokens = DeviceToken::where('user_id', $notifiable->id)->exists();
            if ($hasDeviceTokens) {
                $defaultChannels[] = FcmChannel::class;
            }

            $notification->viaOverride($defaultChannels);
        }

        try {
            Notification::send($notifiable, $notification);
        } catch (\Throwable $e) {
            \Log::error('Notification send failed', [
                'user_id' => $notifiable->id,
                'category' => $category,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function sendToUsers(iterable $notifiables, string $category, array $payload = []): void
    {
        $notification = new GenericNotification($category, $payload);

        $usersWithTokens = [];
        if ($notifiables instanceof \Illuminate\Support\Collection) {
            $userIdsWithTokens = DeviceToken::whereIn('user_id', $notifiables->pluck('id'))->pluck('user_id')->unique()->toArray();
            $usersWithTokens = $notifiables->whereIn('id', $userIdsWithTokens)->pluck('id')->toArray();
        }

        $actorId = data_get($payload, 'actor_id');
        $actor = $actorId ? User::find($actorId) : null;

        foreach ($notifiables as $notifiable) {
            if ($notifiable instanceof User) {
                if ($actor && $notifiable->hasBlockRelationWith($actor)) {
                    continue;
                }

                $channels = config("notifications.categories.{$category}.channels", config('notifications.default_channels', ['database', 'broadcast']));
                if (in_array($notifiable->id, $usersWithTokens)) {
                    $channels[] = FcmChannel::class;
                }
                $clone = new GenericNotification($category, $payload);
                $clone->viaOverride($channels);
                try {
                    $notifiable->notify($clone);
                } catch (\Throwable $e) {
                    \Log::error('Notification send failed', [
                        'user_id' => $notifiable->id,
                        'category' => $category,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }

    public function sendToRoute(string $routeValue, string $category, array $payload = [], string $channel = 'mail'): void
    {
        $anon = (new AnonymousNotifiable())->route($channel, $routeValue);

        $notification = new GenericNotification($category, $payload);

        Notification::send($anon, $notification);
    }
}
