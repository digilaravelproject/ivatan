<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_posts', function (Blueprint $table) {
            $table->boolean('is_exclusive')->default(false)->after('visibility');
            $table->decimal('price', 10, 2)->nullable()->after('is_exclusive');
            $table->enum('exclusive_status', ['pending', 'approved', 'rejected'])->nullable()->after('price');
            $table->text('rejection_reason')->nullable()->after('exclusive_status');
            $table->decimal('override_platform_fee', 8, 2)->nullable()->after('rejection_reason');
            $table->enum('override_platform_fee_type', ['flat', 'percentage'])->nullable()->after('override_platform_fee');
        });
    }

    public function down(): void
    {
        Schema::table('user_posts', function (Blueprint $table) {
            $table->dropColumn([
                'is_exclusive',
                'price',
                'exclusive_status',
                'rejection_reason',
                'override_platform_fee',
                'override_platform_fee_type'
            ]);
        });
    }
};
