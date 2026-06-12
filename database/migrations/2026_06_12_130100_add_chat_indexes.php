<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            $table->index(['chat_id', 'user_id', 'last_read_message_id']);
        });

        Schema::table('user_chat_messages', function (Blueprint $table) {
            $table->index(['chat_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            $table->dropIndex(['chat_id', 'user_id', 'last_read_message_id']);
        });

        Schema::table('user_chat_messages', function (Blueprint $table) {
            $table->dropIndex(['chat_id', 'id']);
        });
    }
};
