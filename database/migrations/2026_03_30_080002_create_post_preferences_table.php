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
        Schema::create('post_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('post_id')->constrained('user_posts')->onDelete('cascade');
            $table->enum('preference', ['interested', 'not_interested']);
            $table->timestamps();

            // One preference per user-post pair
            $table->unique(['user_id', 'post_id']);

            // Fast lookup: "Get all not_interested posts for feed filtering"
            $table->index(['user_id', 'preference']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('post_preferences');
    }
};
