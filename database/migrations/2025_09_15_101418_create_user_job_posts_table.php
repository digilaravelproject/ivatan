<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_job_posts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('employer_id')->constrained('users')->onDelete('cascade'); // employer reference
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('company_name')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_logo')->nullable();
            $table->longText('description');
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->string('location')->nullable();
            $table->string('country')->nullable();
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'internship', 'freelance'])->default('full_time');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('currency', 10)->default('INR');
            $table->boolean('is_remote')->default(false);
            $table->enum('status', ['draft', 'published', 'closed'])->default('published');
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_job_posts');
    }
};
