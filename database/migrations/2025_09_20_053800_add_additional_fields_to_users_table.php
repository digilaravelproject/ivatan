<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 50)->nullable()->after('uuid');
            }

            if (!Schema::hasColumn('users', 'date_of_birth')) {
                $table->date('date_of_birth')->nullable()->after('email_verified_at');
            }

            if (!Schema::hasColumn('users', 'gender')) {
                $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('date_of_birth');
            }

            if (!Schema::hasColumn('users', 'language_preference')) {
                $table->string('language_preference', 10)->default('en')->after('gender');
            }

            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('language_preference');
            }

            if (!Schema::hasColumn('users', 'messaging_privacy')) {
                $table->enum('messaging_privacy', ['everyone', 'followers_only', 'no_one'])->default('everyone')->after('two_factor_enabled');
            }

            if (!Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('messaging_privacy');
            }

            if (!Schema::hasColumn('users', 'last_seen_at')) {
                $table->timestamp('last_seen_at')->nullable()->after('is_online');
            }

            if (!Schema::hasColumn('users', 'device_tokens')) {
                $table->json('device_tokens')->nullable()->after('last_seen_at');
            }

            if (!Schema::hasColumn('users', 'reputation_score')) {
                $table->unsignedInteger('reputation_score')->default(0)->after('device_tokens');
            }

            if (!Schema::hasColumn('users', 'email_notification_preferences')) {
                $table->json('email_notification_preferences')->nullable()->after('reputation_score');
            }

            if (!Schema::hasColumn('users', 'account_privacy')) {
                $table->enum('account_privacy', ['public', 'private', 'hidden'])->default('public')->after('email_notification_preferences');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'date_of_birth',
                'gender',
                'language_preference',
                'two_factor_enabled',
                'messaging_privacy',
                'is_online',
                'last_seen_at',
                'device_tokens',
                'reputation_score',
                'email_notification_preferences',
                'account_privacy',
            ]);
        });
    }
};
