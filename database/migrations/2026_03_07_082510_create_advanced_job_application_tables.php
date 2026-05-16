<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('resume_headline')->nullable();
            $table->json('skills_list')->nullable();
            $table->string('contact_no')->nullable();
            $table->timestamps();
        });

        Schema::create('user_employments', function (Blueprint $table) {
            $table->id();
            $table->morphs('employable'); // employable_id and employable_type
            $table->boolean('is_current_employment')->default(false);
            $table->string('company_name');
            $table->string('job_title');
            $table->date('joining_date')->nullable();
            $table->date('worked_till')->nullable();
            $table->text('job_description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_educations', function (Blueprint $table) {
            $table->id();
            $table->morphs('educationable'); // educationable_id and educationable_type
            $table->string('university_name');
            $table->string('course_name');
            $table->string('course_type')->nullable(); // Full-time, Part-time
            $table->string('course_duration')->nullable();
            $table->string('percentage_cgpa')->nullable();
            $table->timestamps();
        });

        Schema::table('user_job_applications', function (Blueprint $table) {
            $table->string('resume_headline')->nullable()->after('resume_media_id');
            $table->json('skills_list')->nullable()->after('resume_headline');
            $table->string('contact_no')->nullable()->after('skills_list');
        });
    }

    public function down(): void
    {
        Schema::table('user_job_applications', function (Blueprint $table) {
            $table->dropColumn(['resume_headline', 'skills_list', 'contact_no']);
        });
        Schema::dropIfExists('user_educations');
        Schema::dropIfExists('user_employments');
        Schema::dropIfExists('user_profiles');
    }
};
