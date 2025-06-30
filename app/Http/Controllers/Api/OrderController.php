<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // fetch all orders for authenticated user
    public function index()
    {
        return Order::with('items.product') // items.product => fetch related product details for each order item
            ->where('user_id', Auth::id()) // get id of the authenticated user
            ->get();
    }

    // make a new order
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1', // at least one item is required
            'items.*.product_id' => 'required|exists:products,id', // product_id must exist in products table
            'items.*.quantity' => 'required|integer|min:1', // quantity must be a positive integer
            'items.*.price' => 'required|numeric|min:0', // price must be a non-negative number
            'total_price' => 'required|numeric|min:0', // total_price must be a non-negative number
            'shipping_address_id' => 'required|exists:shipping_addresses,id', // shipping_address_id must exist in shipping_addresses table
        ]);

        //Calculate total price on server
        $calculatedTotal = collect($request->items)
            ->sum(fn($item) => $item['price'] * $item['quantity']);

        DB::beginTransaction(); // Start a database transaction

        //try catch block to handle any exceptions during order creation
        try {
            $order = Order::create([
                'user_id' => Auth::id(), //get ID of authenticated user
                'status' => 'pending',
                'total_price' => $calculatedTotal, // total price calculated from items
                'shipping_address_id' => $request->shipping_address_id,
                'payment_id' => null,
            ]);

            // foreach loop used for array of items to create order items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            }
            DB::commit(); // If everything above was successful, all changes are permanently saved in the database.

            $successMessage = [
                'Message' => 'Order placed successfully',
                'Order ID' => $order->id,
                'Total Price' => $request->total_price,
                'Shipping Address ID' => $request->shipping_address_id,
                'Items' => $request->items,
                'order' => Order::with('items.product')->find($order->id),
            ];
            return response()->json([$successMessage], 201);
        } catch (\Exception $e) { //in case of exception
            DB::rollBack(); // Rollback the transaction if there is an error
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
