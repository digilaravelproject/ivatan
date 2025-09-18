<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained('ads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('INR');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('razorpay_order_id')->nullable();
            $table->string('razorpay_payment_id')->nullable();
            $table->string('razorpay_signature')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ad_payments');
    }
};
