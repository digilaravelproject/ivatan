<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            $table->index(['chat_id', 'user_id', 'last_read_message_id'], 'ucp_chat_user_read_idx');
        });

        Schema::table('user_chat_messages', function (Blueprint $table) {
            $table->index(['chat_id', 'id'], 'ucm_chat_id_idx');
        });
    }

    public function down(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            $table->dropIndex('ucp_chat_user_read_idx');
        });

        Schema::table('user_chat_messages', function (Blueprint $table) {
            $table->dropIndex('ucm_chat_id_idx');
        });
    }
};
