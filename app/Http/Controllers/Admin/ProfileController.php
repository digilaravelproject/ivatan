<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        return view('admin.profile.edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|max:1024',
        ]);
        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

       if ($request->hasFile('profile_photo')) {
        // Delete old photo if exists
        if ($user->profile_photo_path  && Storage::disk('s3')->exists($user->profile_photo_path)) {
            Storage::disk('s3')->delete($user->profile_photo_path);
        }

        // Store new photo in S3
        $path = $request->file('profile_photo')->store('profile-photos', 's3');

        // Make it public
        Storage::disk('s3')->setVisibility($path, 'public');
        $user->profile_photo_path = $path;
    }

    $user->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
