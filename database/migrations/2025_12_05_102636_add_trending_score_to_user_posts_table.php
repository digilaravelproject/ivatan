<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_posts', function (Blueprint $table) {
            // Index add karna mat bhulna, varna sorting slow hogi
            $table->double('trending_score')->default(0)->index()->after('visibility');
        });
    }

    public function down(): void
    {
        Schema::table('user_posts', function (Blueprint $table) {
            $table->dropColumn('trending_score');
        });
    }
};
