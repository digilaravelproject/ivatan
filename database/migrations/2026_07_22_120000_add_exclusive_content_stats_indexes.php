<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exclusive_content_purchases', function (Blueprint $table) {
            $table->index(['user_post_id', 'status', 'created_at'], 'ecp_post_status_created_idx');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->index(['viewable_type', 'viewable_id', 'user_id', 'created_at'], 'views_morph_user_created_idx');
        });

        Schema::table('user_posts', function (Blueprint $table) {
            $table->index(['user_id', 'is_exclusive', 'status', 'type'], 'up_user_exclusive_status_type_idx');
        });
    }

    public function down(): void
    {
        Schema::table('exclusive_content_purchases', function (Blueprint $table) {
            $table->dropIndex('ecp_post_status_created_idx');
        });

        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_morph_user_created_idx');
        });

        Schema::table('user_posts', function (Blueprint $table) {
            $table->dropIndex('up_user_exclusive_status_type_idx');
        });
    }
};
