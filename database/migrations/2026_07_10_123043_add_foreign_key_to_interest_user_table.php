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
        Schema::table('interest_user', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['interest_id']);

            // Re-create the foreign key constraint with RESTRICT behavior and custom constraint name
            $table->foreign('interest_id', 'fk_interest_user_interest')
                ->references('id')
                ->on('interests')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('interest_user', function (Blueprint $table) {
            // Drop the custom foreign key constraint
            $table->dropForeign('fk_interest_user_interest');

            // Restore the original foreign key constraint
            $table->foreign('interest_id')
                ->references('id')
                ->on('interests')
                ->onDelete('cascade');
        });
    }
};
