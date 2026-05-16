<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Polymorphic relation: can comment on post, reel, etc.
            $table->morphs('commentable'); // commentable_type, commentable_id

            // For replies (self-nesting)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('comments')
                ->nullOnDelete();

            $table->text('body');
            $table->enum('status', ['active', 'deleted', 'flagged'])->default('active');
            $table->unsignedBigInteger('like_count')->default(0); // optional performance boost
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('comments');
    }
};


// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('comments', function (Blueprint $table) {

//                 $table->id();
//                 $table->foreignId('user_id')->constrained()->onDelete('cascade');
//                 $table->foreignId('post_id')->constrained()->onDelete('cascade');
//                 $table->nullableMorphs('parent'); // For threaded comments
//                 $table->text('content');
//                 $table->enum('status', ['active', 'deleted', 'flagged'])->default('active');
//                 $table->timestamps();

//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('comments');
//     }
// };
