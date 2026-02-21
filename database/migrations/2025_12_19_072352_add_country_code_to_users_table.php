<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 'phone' ke baad add hoga. Nullable rakha hai taaki existing data crash na ho.
            $table->string('country_code', 5)->nullable()->after('phone');

            // Optional: ISO Code bhi rakh lo future ke liye (e.g., 'IN', 'US')
            $table->string('iso_code', 3)->nullable()->after('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['country_code', 'iso_code']);
        });
    }
};
