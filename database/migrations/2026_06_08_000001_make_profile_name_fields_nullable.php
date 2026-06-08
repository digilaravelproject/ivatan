<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employer_details', function (Blueprint $table) {
            $table->string('company_name')->nullable()->change();
        });

        Schema::table('content_creator_details', function (Blueprint $table) {
            $table->string('channel_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employer_details', function (Blueprint $table) {
            $table->string('company_name')->nullable(false)->change();
        });

        Schema::table('content_creator_details', function (Blueprint $table) {
            $table->string('channel_name')->nullable(false)->change();
        });
    }
};
