<?php

namespace App\Http\Controllers\Admin\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Ecommerce\UserProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    /**
     * Show list of products with optional status filter and search.
     */
    public function index(Request $request)
    {
        $query = UserProduct::with(['seller']);

        // filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // simple search on title, sku or uuid (if you have sku)
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($qBuilder) use ($q) {
                $qBuilder->where('title', 'like', "%{$q}%")
                    ->orWhere('slug', 'like', "%{$q}%")
                    ->orWhere('uuid', 'like', "%{$q}%");
            });
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.product.index', compact('products'));
    }
    public function show(Request $request, UserProduct $product)
    {
        $product->load('images', 'seller'); // Eager load relationships

        return view('admin.product.show', compact('product'));
    }


    /**
     * Approve a pending product
     */
    public function approve(UserProduct $product, Request $request)
    {
        $product->status = 'approved';
        $product->admin_note = $product->admin_note ?? null; // keep existing note if any
        $product->save();

        // optional: dispatch notification job to seller

        return redirect()->back()->with('success', 'Product approved successfully.');
    }

    /**
     * Reject product with admin note
     */
    public function reject(UserProduct $product, Request $request)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:2000',
        ]);

        $product->status = 'rejected';
        $product->admin_note = $request->admin_note;
        $product->save();

        // optional: notify seller about rejection and note

        return redirect()->back()->with('success', 'Product rejected and seller notified.');
    }
}
