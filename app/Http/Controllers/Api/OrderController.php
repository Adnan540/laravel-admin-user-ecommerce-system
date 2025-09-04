<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Events\OrderPlaced;

class OrderController extends Controller
{
    // fetch all orders for authenticated user
    public function index()
    {
        return Order::with('items.product') // items.product => fetch related product details for each order item
            ->where('user_id', Auth::id()) // get id of the authenticated user
            ->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
        ]);

        // Calculate total on server
        $calculatedTotal = collect($request->items)
            ->sum(fn($item) => $item['price'] * $item['quantity']);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_price' => $calculatedTotal,
                'shipping_address_id' => $request->shipping_address_id,
                'payment_id' => null,
            ]);

            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            }

            DB::commit();

            // Fire event AFTER commit (email + admin notifications handled by listeners)
            event(new \App\Events\OrderPlaced($order->fresh()));

            // Optional: notify admins instantly (separate from email)
            User::whereIn('role', ['admin', 'superadmin'])->get()
                ->each(fn($u) => $u->notify(new NewOrderNotification($order)));

            return response()->json([
                'message' => 'Order placed successfully',
                'data' => $order->load('items.product')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to place order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // fetch a specific order by ID for authenticated user
    public function show($id)
    {
        $order = Order::with('items.product') // items.product => fetch related product details for each order item
            ->where('id', $id)
            ->where('user_id', Auth::id()) // get id of the authenticated user
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json($order);
    }
    // update the status of an order
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string|in:pending,processing,shipped,delivered,cancelled', // status must be one of the specified values
        ]);

        $order = Order::where('id', $id)->where('user_id', Auth::id())->first();
        if ($order == null) {
            return response()->json([
                'Message' => 'Order not been found',
                'Status' => '404 Not Found'
            ], 404);
        } else {
            $order->status = $request->status;  // update the status of the order
            $order->save(); // save the changes to the database
            return response()->json([
                'Message' => 'Order status updated successfully',
                'Order ID' => $order->id,
                'New Status' => $order->status
            ], 200);
        }
    }
}
