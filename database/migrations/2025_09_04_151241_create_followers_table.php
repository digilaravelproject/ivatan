<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowersTable extends Migration
{
    public function up()
    {
        Schema::create('followers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('follower_id')
                ->constrained('users')
                ->onDelete('cascade'); // when user is deleted, remove followings

            $table->foreignId('following_id')
                ->constrained('users')
                ->onDelete('cascade'); // when followed user is deleted, remove followers

            $table->timestamps();

            $table->unique(['follower_id', 'following_id']);
            $table->index('follower_id');
            $table->index('following_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('followers');
    }
}
