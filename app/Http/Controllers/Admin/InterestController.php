<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    public function index()
    {
        $interests = Interest::withCount('users')->latest()->paginate(10);
        return view('admin.interests.index', compact('interests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:interests,name|max:50',
            'description' => 'nullable|string|max:255'
        ]);

        Interest::create([
            'name' => $request->name,
            'description' => $request->description
        ]);

        return back()->with('success', 'Interest added successfully.');
    }

    public function destroy(Interest $interest)
    {
        $interest->delete();
        return back()->with('success', 'Interest deleted.');
    }
}
