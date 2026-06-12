<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_payments', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('currency');
            $table->string('gateway_order_id')->nullable()->after('gateway');
            $table->string('gateway_payment_id')->nullable()->after('gateway_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('ad_payments', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_order_id', 'gateway_payment_id']);
        });
    }
};
