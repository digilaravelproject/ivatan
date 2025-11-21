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
        Schema::table('interests', function (Blueprint $table) {
            $table->foreignId('interest_category_id')
                ->nullable()
                ->constrained('interest_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interests', function (Blueprint $table) {
            // First drop the foreign key
            $table->dropForeign(['interest_category_id']);

            // Then drop the column
            $table->dropColumn('interest_category_id');
        });
    }
};
