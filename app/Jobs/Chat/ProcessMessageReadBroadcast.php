<?php

namespace App\Jobs\Chat;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Events\Chat\MessageRead;
// use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessMessageReadBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public int $chatId;
    public int $userId;
    public int $lastReadMessageId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $chatId, int $userId, int $lastReadMessageId)
    {
        $this->chatId = $chatId;
        $this->userId = $userId;
        $this->lastReadMessageId = $lastReadMessageId;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new MessageRead($this->chatId, $this->userId, $this->lastReadMessageId))->toOthers();
    }
}
