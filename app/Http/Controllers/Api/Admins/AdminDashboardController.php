<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum , is_admin']);
    }

    public function index()
    {
        return response()->json([
            'Message' => 'Welcome to admin dashboard',
            'Status' => 'succeed',
            'Data' => [
                'total_users' => \App\Models\User::count(),
                'total_orders' => \App\Models\Order::count(),
                'total_products' => \App\Models\Product::count(),
                'total_trader_applications' => \App\Models\TraderApplication::count(),
                'total_payments' => \App\Models\Payment::count(),
                'total_shipping_methods' => \App\Models\ShippingMethod::count(),
            ]
        ])->setStatusCode(200, 'Admin Dashboard Data Retrieved Successfully', [
            'Content-Type' => 'application/json'
        ]);
    }
}
