<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Validator;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware((['auth:sanctum', 'is_admin']));
    }

    // Show all orders with their items and user details
    public function index()
    {
        $orders = Order::with('items', 'user')->latest()->get();
        $successResponse = [
            'message' => 'List of all orders',
            'data' => $orders,
            'status' => 'success'
        ];
        return response()->json($successResponse, 200);
    }

    // Show a specific order by ID
    public function show($id)
    {
        $order = Order::with('items', 'user', 'shippingAddress', 'payment')->find($id);
        if (!$order) { // check if order exist
            $failedMessage = [
                'message' => 'Order not found',
                'status' => 'error'
            ];

            return response()->json($failedMessage, 404);
        }
        return response()->json([
            'message' => 'Order details',
            'data' => $order,
            'status' => 'success'
        ]);
    }

    // Update the status of an order
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,processing,shipped,deliverd,cancelled',
        ]);
        if ($validator->fails()) {
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 'error'
            ], 422);
        } else {
            $order = Order::find($id);
            if (!$order) { // check if order exist
                return response()->json([
                    'message' => 'Order not found',
                    'status' => 'error'
                ], 404);
            } else {
                // update order status
                $order->status = $request->status;
                $order->save();

                return response()->json([
                    'message' => 'Order has been updated successfully',
                    'data' => $order,
                    'status' => 'success'
                ]);
            }
        }
    }
}
