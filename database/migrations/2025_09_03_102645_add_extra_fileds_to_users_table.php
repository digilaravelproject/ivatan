<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile photo path (S3/local storage ke liye)
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable()->after('password');
            }

            // Role (admin, user, etc.)
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('user')->after('profile_photo_path');
            }

            // Status (active/inactive)
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('role');
            }

            // Is Blocked (admin ban kare to)
            if (!Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('status');
            }

            // Last login timestamp
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_blocked');
            }

            // Phone number (optional future use)
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }

            // Address (optional future use)
            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo_path',
                'role',
                'status',
                'is_blocked',
                'last_login_at',
                'phone',
                'address',
            ]);
        });
    }
};
