<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->index(['user_id', 'created_at', 'id'], 'idx_likes_user_created_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->index(['user_id', 'created_at', 'id'], 'idx_comments_user_created_id');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->index(['user_id', 'viewable_type', 'created_at', 'id'], 'idx_views_user_type_created_id');
        });

        Schema::table('user_orders', function (Blueprint $table) {
            $table->index(['buyer_id', 'created_at', 'id'], 'idx_orders_buyer_created_id');
        });
    }

    public function down(): void
    {
        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex('idx_likes_user_created_id');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('idx_comments_user_created_id');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('idx_views_user_type_created_id');
        });

        Schema::table('user_orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_buyer_created_id');
        });
    }
};
