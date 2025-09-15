<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_job_applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('job_id')->constrained('user_job_posts')->onDelete('cascade');
            $table->foreignId('applicant_id')->constrained('users')->onDelete('cascade');
            $table->text('cover_message')->nullable();
            // Keep both resume_path and resume_media_id to support either approach (file-system or media library)
            $table->string('resume_path')->nullable();
            $table->unsignedBigInteger('resume_media_id')->nullable();
            $table->enum('status', ['applied', 'viewed', 'shortlisted', 'rejected', 'hired'])->default('applied');
            $table->timestamp('applied_at')->nullable();
            $table->timestamps();

            // prevent duplicate applications by same user for same job
            $table->unique(['job_id', 'applicant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_job_applications');
    }
};
