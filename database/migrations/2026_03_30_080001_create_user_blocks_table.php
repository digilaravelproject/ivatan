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
        Schema::create('user_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');           // Blocker
            $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade'); // Blocked
            $table->timestamps();

            // One block record per user pair
            $table->unique(['user_id', 'blocked_user_id']);

            // Fast lookup: "Who did I block?" and "Who blocked me?"
            $table->index('user_id');
            $table->index('blocked_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_blocks');
    }
};
