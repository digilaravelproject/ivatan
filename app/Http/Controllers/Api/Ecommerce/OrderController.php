<?php

namespace App\Http\Controllers\Api\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ecommerce\UserOrder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Exception;

class OrderController extends Controller
{
    use AuthorizesRequests;
    // List logged-in user's orders
    public function index(Request $request)
    {
        try {
            $orders = UserOrder::with('items', 'payment', 'shipping', 'address')
                ->where('buyer_id', $request->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

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

    // Show single order (only if authorized by policy)
    public function show(Request $request, $id)
    {
        try {
            $order = UserOrder::with('items', 'payment', 'shipping', 'buyer', 'address')
                ->findOrFail($id);

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

    // Delete order (only pending & owned)
    public function destroy(Request $request, $id)
    {
        try {
            $order = UserOrder::findOrFail($id);

            $this->authorize('delete', $order);

            $order->delete();

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
