<?php

namespace App\Services;

use App\Models\DeviceToken;
use App\Models\User;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class FcmPushService
{
    protected ?\Kreait\Firebase\Contract\Messaging $messaging = null;

    protected function messaging(): ?\Kreait\Firebase\Contract\Messaging
    {
        if ($this->messaging) {
            return $this->messaging;
        }

        try {
            $credentialsPath = config('firebase.credentials');
            
            if (!file_exists($credentialsPath)) {
                Log::error('FCM credentials file not found: ' . $credentialsPath);
                return null;
            }

            $credentials = json_decode(file_get_contents($credentialsPath), true);
            $factory = (new Factory())->withServiceAccount($credentials);

            $this->messaging = $factory->createMessaging();
        } catch (\Throwable $e) {
            Log::error('FCM initialization failed: ' . $e->getMessage());
            return null;
        }

        return $this->messaging;
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = DeviceToken::where('user_id', $user->id)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }

    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): void
    {
        $messaging = $this->messaging();

        if (!$messaging || empty($tokens)) {
            return;
        }

        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ])
            ->withData(array_merge($data, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']));

        foreach ($tokens as $token) {
            try {
                $messaging->send($message->withChangedTarget('token', $token));
            } catch (NotFound $e) {
                Log::warning('FCM token invalid (removed): ' . substr($token, 0, 20) . '...');
                DeviceToken::where('token', $token)->delete();
            } catch (InvalidMessage $e) {
                Log::error('FCM invalid message for token: ' . $e->getMessage());
            } catch (\Throwable $e) {
                if (str_contains($e->getMessage(), 'SenderId mismatch')) {
                    Log::warning('FCM token invalid (removed): ' . substr($token, 0, 20) . '...');
                    DeviceToken::where('token', $token)->delete();
                } else {
                    Log::error('FCM send failed: ' . $e->getMessage());
                }
            }
        }
    }

    public function sendToMultipleUsers(Collection $users, string $title, string $body, array $data = []): void
    {
        $userIds = $users->pluck('id')->toArray();
        $tokens = DeviceToken::whereIn('user_id', $userIds)
            ->pluck('token')
            ->toArray();

        if (empty($tokens)) {
            return;
        }

        $this->sendToTokens($tokens, $title, $body, $data);
    }
}
