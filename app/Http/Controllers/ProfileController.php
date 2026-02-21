<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Helper to Detect Disk (S3 vs Public)
     */
    private function getStorageDisk()
    {
        if (config('filesystems.disks.s3.key') && config('filesystems.disks.s3.secret')) {
            return 's3';
        }
        return 'public';
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // ✅ SYNC LOGIC & SMART DISK ADDED
        if ($request->hasFile('profile_photo')) {
            // Optional: Validate Image specifically here if not in ProfileUpdateRequest
            $request->validate([
                'profile_photo' => ['nullable', 'image', 'max:1024'],
            ]);

            // 1. Detect Disk
            $disk = $this->getStorageDisk();

            // 2. Clear Old Media (Spatie)
            $user->clearMediaCollection('profile_photo');

            // 3. Add New Media
            $media = $user->addMedia($request->file('profile_photo'))
                ->usingFileName(time() . '_' . $request->file('profile_photo')->getClientOriginalName())
                ->toMediaCollection('profile_photo', $disk);

            // 4. Update User Table with EXACT Spatie path
            // This ensures DB and Media are 100% synced.
            $user->profile_photo_path = $media->id . '/' . $media->file_name;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
