<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            if (!Schema::hasColumn('user_chat_participants', 'is_banned')) {
                $table->boolean('is_banned')->default(false);
            }
            if (!Schema::hasColumn('user_chat_participants', 'banned_at')) {
                $table->timestamp('banned_at')->nullable();
            }
            if (!Schema::hasColumn('user_chat_participants', 'is_muted')) {
                $table->boolean('is_muted')->default(false);
            }
            if (!Schema::hasColumn('user_chat_participants', 'muted_until')) {
                $table->timestamp('muted_until')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_chat_participants', function (Blueprint $table) {
            $table->dropColumn(['is_banned', 'banned_at', 'is_muted', 'muted_until']);
        });
    }
};
