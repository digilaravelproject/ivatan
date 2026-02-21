<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Models\InterestCategory;
use Illuminate\Http\Request;

class InterestController extends Controller
{
    /**
     * Display all interests with categories.
     */
    public function index()
    {
        $interests = Interest::with(['users', 'category'])
            ->latest()
            ->paginate(10);

        $categories = InterestCategory::orderBy('name')->get();

        return view('admin.interests.index', compact('interests', 'categories'));
    }

    /**
     * Store a new interest
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:interests,name|max:50',
            'interest_category_id' => 'required|exists:interest_categories,id',
            'description' => 'nullable|string|max:255'
        ]);

        Interest::create($request->only([
            'name',
            'interest_category_id',
            'description'
        ]));

        return back()->with('success', 'Interest added successfully.');
    }

    /**
     * Delete interest
     */
    public function destroy(Interest $interest)
    {
        $interest->delete();
        return back()->with('success', 'Interest deleted successfully.');
    }


    /**********************************************
     * CATEGORY CRUD SECTION
     ***********************************************/

    /**
     * Store new category from the same page
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:interest_categories,name|max:50'
        ]);

        InterestCategory::create([
            'name' => $request->name
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    /**
     * Update an existing category
     */
    public function updateCategory(Request $request, InterestCategory $category)
    {
        $request->validate([
            'name' => 'required|max:50|unique:interest_categories,name,' . $category->id
        ]);

        $category->update(['name' => $request->name]);

        return back()->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category (interests auto-delete because of ON DELETE CASCADE)
     */
    public function destroyCategory(InterestCategory $category)
    {
        $category->delete();

        return back()->with('success', 'Category deleted along with all related interests.');
    }
}
