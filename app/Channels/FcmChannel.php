<?php

namespace App\Channels;

use App\Models\User;
use App\Notifications\GenericNotification;
use App\Services\FcmPushService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    protected FcmPushService $fcmPushService;

    public function __construct(FcmPushService $fcmPushService)
    {
        $this->fcmPushService = $fcmPushService;
    }

    public function send(object $notifiable, Notification $notification): void
    {
        if (!($notifiable instanceof User)) {
            return;
        }

        if (!method_exists($notification, 'toFcm')) {
            return;
        }

        $fcmData = $notification->toFcm($notifiable);

        if (empty($fcmData)) {
            return;
        }

        try {
            $this->fcmPushService->sendToUser(
                $notifiable,
                $fcmData['title'] ?? 'Notification',
                $fcmData['body'] ?? '',
                $fcmData['data'] ?? []
            );
        } catch (\Throwable $e) {
            Log::error('FCM channel send failed: ' . $e->getMessage());
        }
    }
}
