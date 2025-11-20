<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interest;
use App\Traits\ApiResponse;

class InterestController extends Controller
{
    use ApiResponse;

    public function index()
    {
        // Change: Pass columns inside an array []
        $interests = Interest::select(['id', 'name', 'description'])->get();

        return $this->success($interests, 'Interests list fetched.');
    }
}
