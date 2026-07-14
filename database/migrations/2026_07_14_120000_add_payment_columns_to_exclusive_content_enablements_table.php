<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exclusive_content_enablements', function (Blueprint $table) {
            $table->string('gateway')->nullable()->after('fee_paid');
            $table->string('gateway_transaction_id')->nullable()->after('gateway');
            $table->string('payment_status')->default('pending')->after('gateway_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('exclusive_content_enablements', function (Blueprint $table) {
            $table->dropColumn(['gateway', 'gateway_transaction_id', 'payment_status']);
        });
    }
};
