<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('seller_type'); // products | services | both
            $table->string('business_name')->nullable();
            $table->text('business_description')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_phone')->nullable();
            $table->text('business_address')->nullable();
            $table->timestamps();

            $table->unique('profile_id');
        });

        Schema::create('employer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('company_name');
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->string('company_website')->nullable();
            $table->string('company_phone')->nullable();
            $table->text('company_address')->nullable();
            $table->timestamps();

            $table->unique('profile_id');
        });

        Schema::create('music_playlist_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('artist_name')->nullable();
            $table->string('stage_name')->nullable();
            $table->string('genre')->nullable();
            $table->string('label')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unique('profile_id');
        });

        Schema::create('content_creator_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('profiles')->cascadeOnDelete();
            $table->string('channel_name');
            $table->string('content_category')->nullable();
            $table->string('platform')->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();

            $table->unique('profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_creator_details');
        Schema::dropIfExists('music_playlist_details');
        Schema::dropIfExists('employer_details');
        Schema::dropIfExists('seller_details');
    }
};
