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
        Schema::create('reels', function (Blueprint $table) {
         $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('video_url', 1024)->nullable();
            $table->string('cover_url', 1024)->nullable();
            $table->text('description')->nullable();
            $table->integer('duration_seconds')->unsigned()->nullable();
            $table->enum('status', ['active','deleted','flagged'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['user_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
