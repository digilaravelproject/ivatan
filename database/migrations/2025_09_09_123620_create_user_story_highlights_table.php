<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_story_highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Specify table name explicitly
            $table->string('title');
            $table->string('cover_media_id')->nullable();
            $table->timestamps();
        });

        Schema::create('highlight_story', function (Blueprint $table) {
            $table->id();
            $table->foreignId('highlight_id')->constrained('user_story_highlights')->cascadeOnDelete();
            $table->foreignId('story_id')->constrained('user_stories')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['highlight_id', 'story_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highlight_story');
        Schema::dropIfExists('user_story_highlights');
    }
};
