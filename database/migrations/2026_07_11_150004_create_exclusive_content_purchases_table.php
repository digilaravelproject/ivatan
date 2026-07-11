<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exclusive_content_purchases', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_post_id')->constrained('user_posts')->onDelete('cascade');
            
            $table->decimal('creator_price', 10, 2);
            $table->decimal('platform_fee_charged', 10, 2)->default(0);
            $table->decimal('gateway_charge_amount', 10, 2)->default(0);
            $table->enum('gateway_charge_bearer', ['buyer', 'creator', 'platform'])->default('buyer');
            $table->decimal('final_paid_amount', 10, 2);
            
            $table->string('gateway_transaction_id')->nullable();
            $table->string('gateway')->nullable();
            
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            $table->index(['buyer_id', 'user_post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exclusive_content_purchases');
    }
};
