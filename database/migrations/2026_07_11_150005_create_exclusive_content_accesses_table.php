<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exclusive_content_accesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_post_id')->constrained('user_posts')->onDelete('cascade');
            $table->foreignId('purchase_id')->constrained('exclusive_content_purchases')->onDelete('cascade');
            
            $table->timestamp('granted_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'user_post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exclusive_content_accesses');
    }
};
