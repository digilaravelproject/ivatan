<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update user_products status enum
        DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive') DEFAULT 'pending'");

        // Update user_services status enum
        DB::statement("ALTER TABLE user_services MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'active', 'inactive') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert user_products status enum
        DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");

        // Revert user_services status enum
        DB::statement("ALTER TABLE user_services MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
