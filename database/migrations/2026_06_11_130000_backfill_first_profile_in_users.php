<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Profile;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chunk users to prevent memory issues
        User::with('profiles')->chunkById(100, function ($users) {
            foreach ($users as $user) {
                $settings = $user->settings ?? [];
                
                // If first_profile is already set, skip
                if (isset($settings['first_profile'])) {
                    continue;
                }

                // Find the first non-personal profile created for this user
                $registrationProfile = $user->profiles->first(fn($p) => $p->type !== 'personal');
                $firstProfile = $registrationProfile ? $registrationProfile->type : 'personal';

                $settings['first_profile'] = $firstProfile;
                $user->settings = $settings;
                $user->save();

                // If they registered with a non-personal profile, deactivate the default personal profile
                if ($firstProfile !== 'personal') {
                    Profile::where('user_id', $user->id)
                        ->where('type', 'personal')
                        ->update(['is_active' => false]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::chunkById(100, function ($users) {
            foreach ($users as $user) {
                $settings = $user->settings ?? [];
                if (isset($settings['first_profile'])) {
                    unset($settings['first_profile']);
                    $user->settings = $settings;
                    $user->save();
                }
            }
        });
    }
};
