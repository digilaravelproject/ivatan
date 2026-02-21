<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

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

    public function edit()
    {
        return view('admin.profile.edit');
    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:1024',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        // ✅ SMART DISK & SYNC LOGIC
        if ($request->hasFile('profile_photo')) {
            // 1. Detect Disk
            $disk = $this->getStorageDisk();

            // 2. Clear Old Media
            $user->clearMediaCollection('profile_photo');

            // 3. Add New Media (Spatie handles the upload to '49/name.png')
            $media = $user->addMedia($request->file('profile_photo'))
                ->usingFileName(time() . '_' . $request->file('profile_photo')->getClientOriginalName())
                ->toMediaCollection('profile_photo', $disk);

            // 4. Update User Table with EXACT Spatie path
            // This ensures DB and Media are 100% synced.
            $user->profile_photo_path = $media->id . '/' . $media->file_name;
        }

        $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully!');
    }
}
