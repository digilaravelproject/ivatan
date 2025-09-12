<?php

namespace App\Http\Controllers\Api\Seller;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserProductRequest;
use App\Http\Requests\UpdateUserProductRequest;
use App\Models\Ecommerce\UserProduct;
use App\Models\Ecommerce\UserProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserProductController extends Controller
{
    // List seller's own products
    // public function index(Request $request)
    // {
    //     $products = UserProduct::where('seller_id', $request->user()->id)->with('images')->get();
    //     return response()->json($products);
    // }
    public function index(Request $request)
    {
        $user = $request->user();

        // Eager load images to avoid N+1 query problem
        $products = UserProduct::where('seller_id', $user->id)
            ->with('images')
            ->get();

        return response()->json($products);
    }

  public function show(Request $request, $id)
{
    $user = $request->user();

    $product = UserProduct::where('seller_id', $user->id)->with('images', 'seller')->find($id);

    if (!$product) {
        return response()->json(['message' => 'Product not found.'], 404);
    }

    return response()->json($product);
}




    // store
    public function store(StoreUserProductRequest $request)
    {
        $user = $request->user();

        $product = UserProduct::create([
            'uuid'        => (string) Str::uuid(),
            'seller_id'   => $user->id,
            'title'       => $request->title,
            'slug'        => Str::slug($request->title) . '-' . Str::random(8),
            'description' => $request->description ?? null,
            'price'       => $request->price,
            'stock'       => $request->stock ?? 0,
            'status'      => 'pending',
        ]);

        // cover image processed by ImageHelper
        if ($request->hasFile('cover_image')) {
            $path = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'products/cover');
            $product->cover_image = $path;
            $product->save();
        }

        // gallery images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = ImageHelper::uploadEcomImage($img, $user->id, 'products/gallery');
                UserProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                ]);
            }
        }

        return response()->json(['success' => true, 'product' => $product->load('images')], 201);
    }

    // update
   public function update(UpdateUserProductRequest $request, UserProduct $product)
{
    $user = $request->user();

    if ($product->seller_id !== $user->id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $update = $request->only(['title', 'description', 'price', 'stock']);

    if (empty($update)) {
        return response()->json(['error' => 'No valid fields provided to update.'], 422);
    }

    // if (isset($update['title'])) {
    //     $update['slug'] = Str::slug($update['title']) . '-' . Str::random(8);
    // }

    $updated = $product->update($update);

    if (!$updated) {
        return response()->json(['error' => 'Failed to update product.'], 500);
    }

    // Cover image replace
    if ($request->hasFile('cover_image')) {
        ImageHelper::deleteEcomImage($product->cover_image);
        $path = ImageHelper::uploadEcomImage($request->file('cover_image'), $user->id, 'products/cover');
        $product->cover_image = $path;
        $product->save();
    }

    // Add new gallery images
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $img) {
            $path = ImageHelper::uploadEcomImage($img, $user->id, 'products/gallery');
            UserProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
            ]);
        }
    }

    // Remove selected images (optional)
    if ($request->filled('remove_image_ids')) {
        $ids = $request->input('remove_image_ids', []);
        $images = UserProductImage::whereIn('id', $ids)->where('product_id', $product->id)->get();
        foreach ($images as $img) {
            ImageHelper::deleteEcomImage($img->image_path);
            $img->delete();
        }
    }

    return response()->json(['success' => true, 'product' => $product->load('images')]);
}


    // destroy (also delete images)
    public function destroy(Request $request, UserProduct $product)
    {
        $user = $request->user();
        if ($product->seller_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // delete cover and gallery files
        ImageHelper::deleteEcomImage($product->cover_image);
        foreach ($product->images as $img) {
            ImageHelper::deleteEcomImage($img->image_path);
            $img->delete();
        }

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted']);
    }
}
