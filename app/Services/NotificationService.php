<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Send a notification to a single notifiable (e.g., User model).
     *
     * @param Authenticatable|Model $notifiable
     * @param string $category Custom category for the notification
     * @param array $payload Custom data payload for the notification
     * @param array|null $channels Optional override for notification channels
     */
    // public function sendToUser($notifiable, string $category, array $payload = [], ?array $channels = null): void
    // {
    //     $notification = new GenericNotification($category, $payload);

    //     // If you need to override channels, you could handle this via payload and logic inside the notification
    //     if ($channels) {
    //         $notification->viaOverride($channels); // You can define this method in GenericNotification
    //     }

    //     Notification::send($notifiable, $notification);
    // }

    public function sendToUser(User $notifiable, string $category, array $payload = [], ?array $channels = null): void
    {
        if (! $notifiable instanceof User) {
            \Log::warning('Notifiable is not a User instance');
            return;
        }

        // \Log::info("Class uses:", class_uses_recursive($notifiable));

        // \Log::info('Notifiable class: ' . get_class($notifiable));
        \Log::info('Implements Notifiable?', [
            'result' => method_exists($notifiable, 'routeNotificationForDatabase'),
        ]);

        \Log::info('Sending notification', [
            'user_id' => $notifiable->id ?? null,
            'category' => $category,
            'payload' => $payload,
            'channels' => $channels,
        ]);

        if (method_exists($notifiable, 'notify')) {
    \Log::info('User can be notified.');
} else {
    \Log::warning('User cannot be notified.');
}

        $notification = new GenericNotification($category, $payload);

        if ($channels) {
            $notification->viaOverride($channels);
        }

        try {
            Notification::send($notifiable, $notification);
        } catch (\Throwable $e) {
            \Log::error('Notification send failed', ['error' => $e->getMessage()]);
        }
    }


    /**
     * Send a notification to multiple users (e.g., a collection or array of notifiables).
     *
     * @param iterable $notifiables
     * @param string $category
     * @param array $payload
     */
    public function sendToUsers(iterable $notifiables, string $category, array $payload = []): void
    {
        $notification = new GenericNotification($category, $payload);

        Notification::send($notifiables, $notification);
    }

    /**
     * Send a notification to an external route (e.g., email, phone) using AnonymousNotifiable.
     *
     * @param string $routeValue Route destination (email address, phone number, etc.)
     * @param string $category
     * @param array $payload
     * @param string $channel Notification channel name ('mail', 'nexmo', etc.)
     */
    public function sendToRoute(string $routeValue, string $category, array $payload = [], string $channel = 'mail'): void
    {
        $anon = (new AnonymousNotifiable())->route($channel, $routeValue);

        $notification = new GenericNotification($category, $payload);

        Notification::send($anon, $notification);
    }
}
