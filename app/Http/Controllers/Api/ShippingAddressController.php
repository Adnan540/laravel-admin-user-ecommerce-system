<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;

class ShippingAddressController extends Controller
{
    //index
    public function index()
    {
        return ShippingAddress::where('user_id', Auth::id())->get();
    }

    //store
    public function store(Request $request)
    {
        $data = $request->validate([
            'address_line1' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'postal_code' => 'required|string',
            'country' => 'required|string',
            'phone' => 'required|string|max:15',
        ]);
        $existing = ShippingAddress::where([
            ['user_id', Auth::id()],
            ['address_line1', $request->address_line1],
            ['city', $request->city],
            ['postal_code', $request->postal_code]
        ])->first();

        if ($existing) {
            return response()->json([
                'message' => 'This address already exists.',
                'address_id' => $existing->id
            ], 409);
        }


        $data['user_id'] = Auth::id(); // Set the user_id to the authenticated user's ID

        $address = ShippingAddress::create($data);
        $successMessage = [
            'Message' => 'Shipping address has been added successfully',
            'Address ID' => $address->id,
            'Address' => $address
        ];
        return response()->json($successMessage, 201);
    }

    //update
    public function update(Request $request, $id)
    {
        $address = ShippingAddress::where('user_id', Auth::id())->findOrFail($id);
        $address->update($request->all());

        return response()->json($address);
    }

    //destory
    public function destroy($id)
    {
        $address = ShippingAddress::where('user_id', Auth::id())->findOrFail($id);
        $address->delete();

        return response()->json(['message' => 'Address deleted']);
    }
}
