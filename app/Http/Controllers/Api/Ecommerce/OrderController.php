<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Ecommerce\OrderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    use AuthorizesRequests;

    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * List logged-in user's orders
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $orders = $this->orderService->listOrders($request->user());
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show single order
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $order = $this->orderService->showOrder($id);

            $this->authorize('view', $order);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to view this order.'
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete order (only pending & owned)
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Fetch order to check authorization policy first
            $order = $this->orderService->showOrder($id);

            $this->authorize('delete', $order);

            $this->orderService->deleteOrder($id, $request->user());

            return response()->json([
                'success' => true,
                'message' => 'Order deleted successfully.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this order.'
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete the order.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
