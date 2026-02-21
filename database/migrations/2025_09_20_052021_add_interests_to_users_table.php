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

            // ✅ Option 2: Default ["entertainment"]
            $table->json('interests')->nullable()->default(json_encode(['entertainment']));
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('interests');
        });
    }
};
