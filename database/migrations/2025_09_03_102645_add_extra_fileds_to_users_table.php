<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Add UUID
            if (!Schema::hasColumn('users', 'uuid')) {
                $table->uuid('uuid')->unique()->after('id');
            }

            // Change profile_photo_path to profile_pic
            if (!Schema::hasColumn('users', 'profile_photo_path')) {

                $table->string('profile_photo_path', 1024)->nullable()->after('password');
            }

            // Add bio
            if (!Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('profile_photo_path');
            }

            // Social counters
            if (!Schema::hasColumn('users', 'followers_count')) {
                $table->unsignedInteger('followers_count')->default(0)->after('bio');
            }

            if (!Schema::hasColumn('users', 'following_count')) {
                $table->unsignedInteger('following_count')->default(0)->after('followers_count');
            }

            if (!Schema::hasColumn('users', 'posts_count')) {
                $table->unsignedInteger('posts_count')->default(0)->after('following_count');
            }

            // Replace role enum with role_id foreign key
            // if (Schema::hasColumn('users', 'role')) {
            //     $table->dropColumn('role');
            // }


            // Add settings (json)
            if (!Schema::hasColumn('users', 'settings')) {
                $table->json('settings')->nullable()->after('following_count');
            }

            // Status (active/inactive)
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('bio');
            }

            //  Is Blocked (admin ban kare to)
            if (!Schema::hasColumn('users', 'is_blocked')) {
                $table->boolean('is_blocked')->default(false)->after('status');
            }



            // Last login timestamp
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_blocked');
            }
            // Address (optional future use)
            if (!Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('is_blocked');
            }
            // Phone number (optional future use)
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            // Add soft deletes (if not already in earlier migration)
            if (!Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Drop newly added columns
            $columns = [
                'uuid',
                'profile_photo_path',
                'bio',
                'followers_count',
                'following_count',
                'posts_count',

                'settings',
                'is_blocked',
                'last_login_at',
                'deleted_at',
                'status',
                'phone',

                'is_verified',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }


        });
    }
};




// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         Schema::table('users', function (Blueprint $table) {
//             // Profile photo path (S3/local storage ke liye)
//             if (!Schema::hasColumn('users', 'profile_photo_path')) {
//                 $table->string('profile_photo_path')->nullable()->after('password');
//             }

//             // Role (admin, user, etc.)
//             if (!Schema::hasColumn('users', 'role')) {
//                 $table->enum('role',['admin','moderator','user'])->default('user')->after('profile_photo_path');
//             }


//             // Is Blocked (admin ban kare to)
//             if (!Schema::hasColumn('users', 'is_blocked')) {
//                 $table->boolean('is_blocked')->default(false)->after('status');
//             }

//             // Last login timestamp
//             if (!Schema::hasColumn('users', 'last_login_at')) {
//                 $table->timestamp('last_login_at')->nullable()->after('is_blocked');
//             }

//             // Phone number (optional future use)
//             if (!Schema::hasColumn('users', 'phone')) {
//                 $table->string('phone')->nullable()->after('email');
//             }

//             // Address (optional future use)
//             if (!Schema::hasColumn('users', 'address')) {
//                 $table->text('address')->nullable()->after('phone');
//             }
//             // Address (optional future use)
//             if (!Schema::hasColumn('users', 'is_verified')) {
//                 $table->boolean('is_verified')->default(false)->after('address');
//             }
//             $table->softDeletes();
//         });
//     }

//     public function down(): void
//     {
//         Schema::table('users', function (Blueprint $table) {
//             $table->dropColumn([
//                 'profile_photo_path',
//                 'role',
//                 'status',
//                 'is_blocked',
//                 'last_login_at',
//                 'phone',
//                 'address',
//                 'is_verified',
//             ]);
//              $table->dropSoftDeletes();
//         });
//     }
// };
