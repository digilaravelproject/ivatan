<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_busy')) {
                $table->boolean('is_busy')->default(false)->after('is_online');
            }
            if (!Schema::hasColumn('users', 'busy_status')) {
                $table->string('busy_status', 50)->nullable()->after('is_busy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_busy')) {
                $table->dropColumn('is_busy');
            }
            if (Schema::hasColumn('users', 'busy_status')) {
                $table->dropColumn('busy_status');
            }
        });
    }
};
