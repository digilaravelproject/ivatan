<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('
                CREATE UNIQUE INDEX user_subscriptions_active_profile_unique
                ON user_subscriptions (profile_id)
                WHERE status IN (\'active\', \'past_due\')
            ');
        } catch (QueryException $e) {
            // MariaDB < 10.5 / SQLite do not support partial unique indexes.
            // Fall back to app-level enforcement (lockForUpdate in purchase + check in profile switch).
        }
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropIndex('user_subscriptions_active_profile_unique');
        });
    }
};
