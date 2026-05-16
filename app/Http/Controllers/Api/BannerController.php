<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Get banners (image / video)
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // image | video | null

        $banners = Banner::query()
            ->where('is_active', true)
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->latest()
            ->get()
            ->map(function ($banner) {
                return [
                    'id' => $banner->id,
                    'title' => $banner->title,
                    'type' => $banner->type,
                    'media_url' => url('storage/'.$banner->file),
                    'created_at' => $banner->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Banners fetched successfully',
            'data' => $banners,
        ]);
    }
}
