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
        Schema::create('user_shippings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('order_id')->constrained('user_orders')->onDelete('cascade');
            $table->string('provider')->nullable(); // Shiprocket, Delhivery etc.
            $table->string('tracking_number')->nullable();
            $table->enum('status', ['pending', 'shipped', 'in_transit', 'delivered', 'cancelled'])->default('pending');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shippings');
    }
};
