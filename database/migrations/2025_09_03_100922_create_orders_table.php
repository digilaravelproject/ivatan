<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /** * Run the migrations. */ public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->json('shipping_address')->nullable();
            $table->enum('status', ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->timestamp('placed_at')->nullable();
            $table->timestamps();
            $table->index(['buyer_id', 'status', 'placed_at']);
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->foreign('seller_id')->references('id')->on('users')->nullOnDelete();
            $table->string('sku')->nullable();
            $table->integer('quantity')->unsigned()->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();
        });
    }
    /** * Reverse the migrations. */ public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
