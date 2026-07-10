<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserSearchResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserSearchController extends Controller
{
    /**
     * Search users by name or username.
     * Integrates Scout search with a database fallback for maximum reliability.
     */
    public function search(Request $request): JsonResponse
    {
        $searchQuery = $request->input('q', '');
        $perPage = (int) $request->input('per_page', 20);

        if (strlen($searchQuery) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters long.',
                'data' => [],
            ], 422);
        }

        $currentUser = Auth::user();

        try {
            $driver = config('scout.driver');
            
            if ($driver === 'database') {
                $users = $this->performDatabaseSearch($searchQuery, $currentUser, $perPage);
            } else {
                $users = User::search($searchQuery)
                    ->query(function ($q) use ($currentUser) {
                        $q->withoutBlocked($currentUser);
                    })
                    ->paginate($perPage);
            }
        } catch (\Throwable $e) {
            Log::warning("Search engine driver failed, falling back to database query: " . $e->getMessage());
            $users = $this->performDatabaseSearch($searchQuery, $currentUser, $perPage);
        }

        return response()->json([
            'success' => true,
            'message' => 'Users search results fetched successfully.',
            'data' => UserSearchResource::collection($users->items()),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'has_more' => $users->hasMorePages(),
            ],
        ]);
    }

    /**
     * Execute SQL database LIKE fallback query.
     */
    protected function performDatabaseSearch(string $searchQuery, ?User $currentUser, int $perPage)
    {
        return User::query()
            ->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('username', 'like', "%{$searchQuery}%");
            })
            ->withoutBlocked($currentUser)
            ->paginate($perPage);
    }
}
