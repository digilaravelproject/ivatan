<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('gateway_customer_id')->nullable()->unique()->after('id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->renameColumn('razorpay_subscription_id', 'gateway_subscription_id');
            $table->renameColumn('razorpay_order_id', 'gateway_order_id');
            $table->renameColumn('razorpay_payment_id', 'gateway_payment_id');
            $table->renameColumn('razorpay_response', 'gateway_response');

            $table->timestamp('next_billing_at')->nullable()->after('ends_at');
            $table->boolean('auto_renew')->default(true)->after('next_billing_at');
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->renameColumn('razorpay_plan_id', 'gateway_plan_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('gateway_customer_id');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->renameColumn('gateway_subscription_id', 'razorpay_subscription_id');
            $table->renameColumn('gateway_order_id', 'razorpay_order_id');
            $table->renameColumn('gateway_payment_id', 'razorpay_payment_id');
            $table->renameColumn('gateway_response', 'razorpay_response');

            $table->dropColumn('next_billing_at');
            $table->dropColumn('auto_renew');
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->renameColumn('gateway_plan_id', 'razorpay_plan_id');
        });
    }
};
