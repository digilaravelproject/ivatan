<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'allow_free_service_listing'],
            [
                'value' => '1',
                'type' => 'boolean',
                'group' => 'services',
                'is_encrypted' => false,
                'description' => 'Allow users to list services without requiring an active subscription plan (1 = allowed, 0 = restricted by plan).',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'allow_free_service_listing')->delete();
    }
};
