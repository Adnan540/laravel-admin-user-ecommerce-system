<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;

class PaymentController extends Controller
{
    // fetch all payments for authenticated user
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())->get();
        if (!$payments || $payments->isEmpty()) {
            $nullresponse = [
                'Message' => 'There are no payments assigned to this user'
            ];
            return response()->json($nullresponse, 404);
        } else {
            $successMessage = [
                'Message' => 'Payments fetched successfully',
                'Payments' => $payments,
                'user_id' => Auth::id()
            ];
            return response()->json($successMessage, 200);
        }
    }

    // create a new payment for an order
    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'method' => 'required|string',
            'transaction_id' => 'nullable|string',
        ]);

        // 🔒 Ensure order belongs to the logged-in user
        $order = Order::where('id', $data['order_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or unauthorized'], 403);
        }

        // 🔒 Prevent double payment
        if ($order->payment_id) {
            return response()->json(['message' => 'Order already paid'], 400);
        }
        // create a new payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'order_id' => $data['order_id'],
            'method' => $data['method'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'payment_status' => true,
            'paid_at' => now(),
        ]);

        // 🔗 Link payment to order
        $order->update(['payment_id' => $payment->id]);

        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment,
        ], 201);
    }


    public function show($id)
    {
        return Payment::where('user_id', Auth::id())->findOrFail($id);
    }
}
