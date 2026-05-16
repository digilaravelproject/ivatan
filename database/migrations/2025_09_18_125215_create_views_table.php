<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->morphs('viewable'); // viewable_id + viewable_type
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'viewable_id', 'viewable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('views');
    }
};
