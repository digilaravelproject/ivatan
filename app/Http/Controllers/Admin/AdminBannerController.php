<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBannerController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');

        $banners = Banner::when(
                $type && $type !== 'all',
                fn ($q) => $q->where('type', $type)
            )
            ->latest()
            ->paginate(12);

        return view('admin.banner.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'type'  => 'required|in:image,video',
            'file'  => 'required|file|mimes:jpg,jpeg,png,mp4,mov',
        ]);

        $path = $request->file('file')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'type'  => $request->type,
            'file'  => $path,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner added successfully.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'type'  => 'required|in:image,video',
            'file'  => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov',
        ]);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($banner->file);
            $banner->file = $request->file('file')->store('banners', 'public');
        }

        $banner->update([
            'title' => $request->title,
            'type'  => $request->type,
        ]);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->file);
        $banner->delete();

        return back()->with('success', 'Banner deleted.');
    }
}
