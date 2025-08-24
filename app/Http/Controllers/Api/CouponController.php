<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    public function index()
    {
        return Coupon::all(); // Return all coupons
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:coupons',
            'discount_percent' => 'required|numeric|min:0|max:100',
            'max_uses' => 'required|integer',
            'expire_at' => 'required|date_format:Y-m-d',
        ]);

        $coupon = Coupon::create($data);
        return response()->json($coupon, 201);
    }


    public function show($id)
    {
        return Coupon::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update($request->all());

        return response()->json($coupon);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json(['message' => 'Coupon deleted']);
    }
}
