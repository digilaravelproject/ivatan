<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // ✅ Option 1: Default NULL
            // $table->json('interests')->nullable()->default(null);

            // ✅ JSON default not supported on MySQL < 8.0.13, using nullable instead
            $table->json('interests')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('interests');
        });
    }
};
