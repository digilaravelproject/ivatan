<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exclusive_content_enablements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->decimal('fee_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected', 'disabled_by_creator', 'disabled_by_admin'])->default('pending');
            $table->decimal('override_platform_fee', 8, 2)->nullable();
            $table->enum('override_platform_fee_type', ['flat', 'percentage'])->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exclusive_content_enablements');
    }
};
