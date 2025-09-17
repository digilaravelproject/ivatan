<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct($message = "This is a test notification")
    {
        $this->message = $message;
    }

    // store in database
    public function via($notifiable)
    {
        return ['database'];
    }

    // data to save in database
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Test Notification',
            'message' => $this->message,
            'user_id' => $notifiable->id,
        ];
    }
}
