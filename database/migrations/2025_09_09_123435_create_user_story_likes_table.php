<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_story_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained('user_stories')->cascadeOnDelete(); // Specify table name explicitly
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Specify table name explicitly
            $table->timestamps();

            $table->unique(['story_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_story_likes');
    }
};
