<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_story_highlights', function (Blueprint $table) {
            // Drop the old varchar column first
            $table->dropColumn('cover_media_id');
        });

        Schema::table('user_story_highlights', function (Blueprint $table) {
            // Add new unsignedBigInteger column, nullable if you want
            $table->unsignedBigInteger('cover_media_id')->nullable()->after('title');

            // Add foreign key constraint
            $table->foreign('cover_media_id')
                ->references('id')
                ->on('media')
                ->onDelete('set null');  // agar media delete ho to null set kare cover_media_id
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_story_highlights', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['cover_media_id']);
            // Drop the unsignedBigInteger column
            $table->dropColumn('cover_media_id');
        });

        Schema::table('user_story_highlights', function (Blueprint $table) {
            // Add back the old varchar column
            $table->string('cover_media_id')->nullable()->after('title');
        });
    }
};
