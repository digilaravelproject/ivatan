<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->index('seller_id');
            $table->index('slug');
            $table->index('uuid');
            $table->index('status');
        });

        Schema::table('user_services', function (Blueprint $table) {
            $table->index('seller_id');
            $table->index('slug');
            $table->index('uuid');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropIndex(['seller_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['uuid']);
            $table->dropIndex(['status']);
        });

        Schema::table('user_services', function (Blueprint $table) {
            $table->dropIndex(['seller_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['uuid']);
            $table->dropIndex(['status']);
        });
    }
};
