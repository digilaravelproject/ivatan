<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            if (!Schema::hasColumn('user_products', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            if (Schema::hasColumn('user_products', 'admin_note')) {
                $table->dropColumn('admin_note');
            }
        });
    }
};
