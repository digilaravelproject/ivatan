<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add Avatar to User Chats (For Group Icons)
        Schema::table('user_chats', function (Blueprint $table) {
            if (!Schema::hasColumn('user_chats', 'avatar_path')) {
                $table->string('avatar_path')->nullable()->after('name');
            }
        });

        // 2. Add Deletion Features to Messages
        Schema::table('user_chat_messages', function (Blueprint $table) {
            // Soft Deletes for "Delete for Everyone"
            if (!Schema::hasColumn('user_chat_messages', 'deleted_at')) {
                $table->softDeletes();
            }

            // JSON column for "Delete for Me"
            if (!Schema::hasColumn('user_chat_messages', 'hidden_for_users')) {
                $table->json('hidden_for_users')->nullable()->after('meta');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_chats', function (Blueprint $table) {
            $table->dropColumn('avatar_path');
        });

        Schema::table('user_chat_messages', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('hidden_for_users');
        });
    }
};
