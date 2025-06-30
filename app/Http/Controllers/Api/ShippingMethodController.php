<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    // Fetch all shipping methods
    public function index()
    {
        return ShippingMethod::all();
    }

    // Create a new shipping method
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string', // shipping method ex: "Standard Shipping", "Express Shipping", pickup, etc.
            'price' => 'required|numeric',
            'estimated_days' => 'required|string', // e.g., "3-5 business days"
        ]);

        $method = ShippingMethod::create($data);
        return response()->json($method, 201);
    }
    // Show a specific shipping method
    public function show($id)
    {
        return ShippingMethod::findOrFail($id);
    }

    // Update an existing shipping method
    public function update(Request $request, $id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->update($request->all());

        return response()->json($method);
    }

    // Delete a shipping method
    public function destroy($id)
    {
        $method = ShippingMethod::findOrFail($id);
        $method->delete();

        return response()->json(['message' => 'Shipping method deleted']);
    }
}
