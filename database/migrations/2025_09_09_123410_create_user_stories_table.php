<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Specify table name explicitly

            $table->enum('type', ['image', 'video'])->nullable();
            $table->text('caption')->nullable();
            $table->json('meta')->nullable();
            $table->unsignedBigInteger('like_count')->default(0);

            $table->timestamp('expires_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stories');
    }
};
