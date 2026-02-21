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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('caption')->nullable();
            $table->enum('type', ['image', 'video', 'carousel'])->default('image');
            $table->json('media_metadata')->nullable();
            $table->enum('status', ['active', 'deleted', 'flagged'])->default('active');
            $table->enum('visibility', ['public', 'private', 'friends'])->default('public');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id', 'status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
