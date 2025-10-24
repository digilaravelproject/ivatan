<?php


namespace App\Http\Controllers\Api\Ad;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Models\Ad;
use App\Models\AdPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class AdController extends Controller
{
    /**
     * Summary of adPackages
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function adPackages(): JsonResponse
    {
        $packages = AdPackage::all();
        return response()->json(['packages' => $packages]);
    }
    // User creates an ad request using an admin-defined package
    /**
     * Summary of store
     * @param StoreAdRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function store(StoreAdRequest $request): JsonResponse
    {
        $user = $request->user();


        $package = AdPackage::findOrFail($request->ad_package_id);


        $mediaSaved = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                $path = $file->store('ads', 'public');
                $mediaSaved[] = $path;
            }
        }


        $ad = Ad::create([
            'user_id' => $user->id,
            'ad_package_id' => $package->id,
            'title' => $request->title,
            'description' => $request->description,
            'media' => $mediaSaved ?: null,
            'status' => 'pending_admin_approval',
        ]);


        return response()->json(['success' => true, 'ad' => $ad], 201);
    }


    public function show(Ad $ad)
    {
        $ad->load('package', 'user');
        return response()->json(['ad' => $ad]);
    }


    // Optionally: list user's ads
    public function myAds(Request $request)
    {
        $ads = $request->user()->ads()->with('package')->latest()->paginate(15);
        return response()->json($ads);
    }
}
