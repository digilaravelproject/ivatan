<?php

namespace App\Http\Controllers\Api\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ecommerce\StoreUserServiceRequest;
use App\Http\Requests\Ecommerce\UpdateUserServiceRequest;
use App\Models\Ecommerce\UserService;
use App\Models\Ecommerce\UserServiceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserServiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $services = UserService::where('seller_id', $user->id)
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($services);
    }

    public function show(Request $request, $id)
    {
        $user = $request->user();

        $service = UserService::where('seller_id', $user->id)
            ->with('images', 'seller')
            ->find($id);

        if (!$service) {
            return response()->json(['message' => 'Service not found.'], 404);
        }

        return response()->json($service);
    }

    public function store(StoreUserServiceRequest $request)
    {
        $user = $request->user();

        $service = UserService::create([
            'uuid'        => (string) Str::uuid(),
            'seller_id'   => $user->id,
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(8),
            'description' => $request->description ?? null,
            'price'       => $request->price,
            'status'      => $request->status ?? 'pending',
        ]);

        // if ($request->hasFile('cover_image')) {
        //     $path = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'services/cover');
        //     $service->cover_image = $path; // note: cover_image column not in old migration — optional store if you have column; otherwise skip
        //     // If old migration doesn't have cover_image, remove above assignment.
        //     $service->save();
        // }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = ImageHelper::uploadEcomImage($img, $user->id, 'services/gallery');
                UserServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['success' => true, 'service' => $service->load('images')], 201);
    }

    public function update(UpdateUserServiceRequest $request, UserService $service)
    {
        $user = $request->user();

        if ($service->seller_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $update = $request->only(['title', 'description', 'price', 'status']);

        if (empty($update)) {
            return response()->json(['error' => 'No valid fields provided to update.'], 422);
        }

        $updated = $service->update($update);

        if (!$updated) {
            return response()->json(['error' => 'Failed to update service.'], 500);
        }

        // if ($request->hasFile('cover_image')) {
        //     // If you actually store cover_image column, delete old; else skip these two lines.
        //     ImageHelper::deleteEcomImage($service->cover_image ?? null);
        //     $path = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'services/cover');
        //     $service->cover_image = $path;
        //     $service->save();
        // }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = ImageHelper::uploadEcomImage($img, $user->id, 'services/gallery');
                UserServiceImage::create([
                    'service_id' => $service->id,
                    'image_path' => $path,
                ]);
            }
        }

        if ($request->filled('remove_image_ids')) {
            $ids = $request->input('remove_image_ids', []);
            $images = UserServiceImage::whereIn('id', $ids)->where('service_id', $service->id)->get();
            foreach ($images as $img) {
                ImageHelper::deleteEcomImage($img->image_path);
                $img->delete();
            }
        }

        return response()->json(['success' => true, 'service' => $service->load('images')]);
    }

    public function destroy(Request $request, UserService $service)
    {
        $user = $request->user();

        if ($service->seller_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // delete images
        foreach ($service->images as $img) {
            ImageHelper::deleteEcomImage($img->image_path);
            $img->delete();
        }

        $service->delete();

        return response()->json(['success' => true, 'message' => 'Service deleted']);
    }
}
