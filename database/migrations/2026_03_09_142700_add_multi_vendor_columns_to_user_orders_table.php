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
        Schema::table('user_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->unsignedBigInteger('seller_id')->nullable()->after('buyer_id');

            $table->foreign('parent_id')->references('id')->on('user_orders')->nullOnDelete();
            $table->foreign('seller_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_orders', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['seller_id']);

            $table->dropColumn('parent_id');
            $table->dropColumn('seller_id');
        });
    }
};
