<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            $table->unsignedInteger('reach_limit')->default(0); // how many unique users
            $table->unsignedInteger('duration_days')->default(7);
            $table->json('targeting')->nullable();
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ad_packages');
    }
};
