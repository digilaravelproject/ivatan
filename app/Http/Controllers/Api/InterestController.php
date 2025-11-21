<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InterestCategory;
use App\Traits\ApiResponse;

class InterestController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $categories = InterestCategory::with('interests:id,interest_category_id,name')
            ->select('id', 'name')
            ->get();

        // Convert to desired format
        $data = $categories->map(function ($cat) {
            return [
                'category'  => $cat->name,
                'interests' => $cat->interests->pluck('name')->toArray()
            ];
        });

        return $this->success($data, "Interests grouped by category.");
    }
}
