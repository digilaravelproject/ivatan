<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id')->nullable()->after('description'); // from media library
            $table->unsignedBigInteger('interest_id')->nullable()->after('media_id');
            $table->foreign('interest_id')->references('id')->on('interests')->nullOnDelete();

            $table->dateTime('start_at')->nullable()->change();
            $table->dateTime('end_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table) {
            $table->dropForeign(['interest_id']);
            $table->dropColumn(['interest_id', 'media_id']);
        });
    }
};
