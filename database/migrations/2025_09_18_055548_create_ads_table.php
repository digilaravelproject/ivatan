<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('ad_package_id')->nullable()->constrained('ad_packages')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('media')->nullable(); // array of stored media paths
            $table->enum('status', [
                'draft',
                'pending_admin_approval',
                'awaiting_payment',
                'pending', // fallback
                'approved',
                'rejected',
                'live',
                'expired',
            ])->default('draft');
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('impressions')->default(0); // quick counter
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('ads');
    }
};
