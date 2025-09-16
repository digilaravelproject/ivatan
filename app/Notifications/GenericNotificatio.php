<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class GenericNotificatio extends Notification implements ShouldQueue
{
    use Queueable;
public string $category; // e.g. "message", "like", "job_application"
    public array $payload;   // arbitrary structured array
    /**
     * Create a new notification instance.
     */
     /**
     * @param string $category
     * @param array $payload
     * @param bool $queueNow optional: if you want immediate synchronous send, handle externally
     */
    public function __construct(string $category, array $payload = [])
    {
        $this->category = $category;
        $this->payload = $payload;
        // default queue settings can be customized here, e.g. connection/queue
        $this->onQueue(config('queue.default'));
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // return ['mail'];
         return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'category' => $this->category,
            'payload' => $this->payload,
            // optional meta that can help clients
            'sent_at' => now()->toISOString(),
        ];
    }
     /**
     * Broadcast payload for realtime clients (Laravel will send it on the notifiable's private channel).
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => (string) \Str::uuid(),
            'category' => $this->category,
            'payload' => $this->payload,
            'notifiable_id' => $notifiable->getKey(),
            'sent_at' => now()->toISOString(),
        ]);
    }
}
