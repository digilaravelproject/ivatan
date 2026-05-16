<?php

namespace App\Jobs\Chat;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Events\Chat\MessageSent;
use App\Models\Chat\UserChatMessage;
// use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public UserChatMessage $message;
    /**
     * Create a new job instance.
     */
    public function __construct(UserChatMessage $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Broadcast using Reverb
        broadcast(new MessageSent($this->message))->toOthers();
    }
}
