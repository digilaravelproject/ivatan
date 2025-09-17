<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Str;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $category;
    public array $payload;

    /**
     * Optional override for delivery channels (e.g., ['mail', 'database']).
     *
     * @var array|null
     */
    protected ?array $channelOverride = null;

    /**
     * Create a new notification instance.
     *
     * @param string $category
     * @param array $payload
     */
    public function __construct(string $category, array $payload = [])
    {
        $this->category = $category;
        $this->payload = $payload;

        // You can customize queue/connection here if needed
        $this->onQueue(config('queue.default'));
    }

    /**
     * Allow overriding the notification channels dynamically.
     *
     * @param array $channels
     * @return $this
     */
    public function viaOverride(array $channels): static
    { \Log::info('Notification channels:', [
            'channels' => $this->channelOverride ?? ['database', 'broadcast']
        ]);
        $this->channelOverride = $channels;
        return $this;
    }

    /**
     * Determine which channels the notification will be delivered on.
     *
     * @param mixed $notifiable
     * @return array
     */
    // public function via(object $notifiable): array
    // {
    //     return $this->channelOverride ?? ['database', 'broadcast'];
    // }
    public function via(object $notifiable): array
    {
        \Log::info('Notification channels:', [
            'channels' => $this->channelOverride ?? ['database', 'broadcast']
        ]);

        return $this->channelOverride ?? ['database', 'broadcast'];
    }



    /**
     * Define the mail representation (if using mail channel).
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You have a new notification')
            ->line("Category: {$this->category}")
            ->line('Open the app to view more details.')
            ->action('View', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Define the database representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
         \Log::info("Saving notification to database", [
        'category' => $this->category,
        'payload' => $this->payload,
    ]);
        return [
            'category' => $this->category,
            'payload' => $this->payload,
            'sent_at' => now()->toISOString(),
        ];
    }

    /**
     * Define the broadcast (real-time) representation of the notification.
     *
     * @param mixed $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => Str::uuid()->toString(),
            'category' => $this->category,
            'payload' => $this->payload,
            'notifiable_id' => $notifiable->getKey(),
            'sent_at' => now()->toISOString(),
        ]);
    }
}
