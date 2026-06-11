<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_call_sessions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('chat_id')->constrained('user_chats')->onDelete('cascade');
            $table->foreignId('caller_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('cascade'); // Nullable for group calls
            $table->enum('type', ['audio', 'video']);
            $table->enum('status', ['ringing', 'accepted', 'rejected', 'busy', 'ended', 'no_answer', 'cancelled'])->default('ringing');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_call_sessions');
    }
};
