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
        Schema::table('activity_log', function (Blueprint $table) {
            // Index on log_name already exists, skip adding it
            // Add missing indexes
            $table->index('causer_id');
            $table->index('subject_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            // Drop only the indexes added above
            $table->dropIndex('activity_log_causer_id_index');
            $table->dropIndex('activity_log_subject_id_index');
            $table->dropIndex('activity_log_created_at_index');
        });
    }
};
