<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add indexes to notifications table for faster queries
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                // composite index for queries like WHERE notifiable_type = X AND notifiable_id = Y AND read_at IS NULL
                $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_notifiable_read_idx');
                $table->index('created_at', 'notifications_created_at_idx');
            });
        }

        // 2) Create notification_unread_counts table (DB-side cache for unread counts)
        if (! Schema::hasTable('notification_unread_counts')) {
            Schema::create('notification_unread_counts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->unsignedBigInteger('unread_count')->default(0);
                $table->timestamps();

                $table->unique('user_id', 'notification_unread_counts_user_unique');
                $table->index('unread_count', 'notification_unread_counts_unread_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('notification_unread_counts')) {
            Schema::dropIfExists('notification_unread_counts');
        }

        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                // Attempt to drop indexes if exist (do not crash if missing)
                try {
                    $table->dropIndex('notifications_notifiable_read_idx');
                } catch (\Throwable $e) {}
                try {
                    $table->dropIndex('notifications_created_at_idx');
                } catch (\Throwable $e) {}
            });
        }
    }
};
