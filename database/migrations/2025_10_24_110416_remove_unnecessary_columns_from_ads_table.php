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
        Schema::table('ads', function (Blueprint $table) {
            // Drop foreign key + column safely
            if (Schema::hasColumn('ads', 'interest_id')) {
                $table->dropConstrainedForeignId('interest_id');
            }

            if (Schema::hasColumn('ads', 'media_id')) {
                $table->dropColumn('media_id');
            }

            if (Schema::hasColumn('ads', 'media')) {
                $table->dropColumn('media');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->bigInteger('media_id')->unsigned()->nullable()->after('media_ids');
            $table->longText('media')->nullable()->after('media_id');
            $table->foreignId('interest_id')->nullable()->constrained('interests')->nullOnDelete()->after('media');
        });
    }
};
