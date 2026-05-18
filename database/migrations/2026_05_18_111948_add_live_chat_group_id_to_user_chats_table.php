<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('user_chats', 'live_chat_group_id')) {
                $table->foreignId('live_chat_group_id')
                    ->nullable()
                    ->constrained('live_chat_groups')
                    ->onDelete('cascade');
            }
            if (!Schema::hasColumn('user_chats', 'chat_mode')) {
                $table->string('chat_mode')->nullable()->after('type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_chats', function (Blueprint $table) {
            $table->dropForeign(['live_chat_group_id']);
            $table->dropColumn(['live_chat_group_id', 'chat_mode']);
        });
    }
};
