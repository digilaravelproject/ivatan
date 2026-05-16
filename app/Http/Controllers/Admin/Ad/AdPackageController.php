<?php

namespace App\Http\Controllers\Admin\Ad;

use App\Http\Controllers\Controller;
use App\Models\AdPackage;
use Illuminate\Http\Request;


class AdPackageController extends Controller
{
    /**
     * Display a listing of the ad packages.
     */
    public function index(Request $request)
    {
        $sortBy = $request->get('sort_by', 'id');
        $direction = $request->get('direction', 'desc');

        // Whitelist of allowed columns
        $allowedSorts = ['id', 'name', 'price', 'duration_days', 'reach_limit'];
        $allowedDirections = ['asc', 'desc'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }

        if (!in_array($direction, $allowedDirections)) {
            $direction = 'desc';
        }

        $packages = AdPackage::orderBy($sortBy, $direction)
            ->paginate(20)
            ->appends($request->query()); // preserve sorting on pagination

        return view('admin.ads.packages.index', compact('packages'));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.ads.packages.create');
    }
    /**
     * Store a newly created ad package.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:1',
                'duration_days' => 'required|integer|min:1',
                'reach_limit' => 'required|integer|min:1',
                'targeting' => 'nullable|string',
            ]);
            if (!empty($validated['targeting'])) {
                $decoded = json_decode($validated['targeting'], true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return redirect()->back()->with('error', 'Invalid JSON in targeting field.')->withInput();
                }

                $validated['targeting'] = $decoded;
            }
            AdPackage::create($validated);

            return redirect()
                ->route('admin.ad.ad-packages.index')
                ->with('success', 'Ad Package created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Ad Package Creation Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'An error occurred while creating the Ad Package.')
                ->withInput();
        }
    }



    /**
     * Display the specified ad package.
     */
    public function show(AdPackage $adPackage)
    {
        // return response()->json($adPackage);
        return view('admin.ads.packages.show', ['package' => $adPackage]);
    }
    /**
     * Show edit form.
     */
    public function edit(AdPackage $adPackage)
    {
        return view('admin.ads.packages.edit', compact('adPackage'));
    }

    /**
     * Update the specified ad package.
     */
    public function update(Request $request, AdPackage $adPackage)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:1',
            'duration_days' => 'sometimes|required|integer|min:1',
            'reach_limit' => 'sometimes|required|integer|min:1',
            'targeting' => 'nullable|string',
        ]);
        if (!empty($validated['targeting'])) {
            $decoded = json_decode($validated['targeting'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->with('error', 'Invalid JSON in targeting field.')->withInput();
            }

            $validated['targeting'] = $decoded;
        }

        $adPackage->update($validated);

        return redirect()
            ->route('admin.ad.ad-packages.index')
            ->with('success', 'Ad Package updated successfully.');
    }


    /**
     * Remove the specified ad package.
     */
    public function destroy(AdPackage $adPackage)
    {
        $adPackage->delete();

        return redirect()
            ->route('admin.ad.ad-packages.index')
            ->with('success', 'Ad Package deleted successfully.');
    }
}
