<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('user_chat_participants', 'last_delivered_message_id')) {
                $table->unsignedBigInteger('last_delivered_message_id')->nullable()->after('last_read_message_id');
                
                $table->foreign('last_delivered_message_id')
                      ->references('id')
                      ->on('user_chat_messages')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            if (Schema::hasColumn('user_chat_participants', 'last_delivered_message_id')) {
                $table->dropForeign(['last_delivered_message_id']);
                $table->dropColumn('last_delivered_message_id');
            }
        });
    }
};
