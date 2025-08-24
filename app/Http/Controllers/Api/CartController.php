<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // show the cart for the authenticated user
    public function index()
    {
        // fetch or create cart for authenticated user or return empty cart
        $cart = Cart::with('items.product')->firstOrCreate(['user_id' => Auth::id()]);
        return response()->json($cart);
    }

    // clear the cart for the authenticated user
    public function clear()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['message' => 'Cart cleared']);
    }

    // create or update a cart item
    public function addItem(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);
        $cart = Cart::firstOrCreate(['user_id' => Auth::id()]);
        $item = CartItem::updateOrCreate(
            ['cart_id' => $cart->id, 'product_id' => $data['product_id']], // find existing item or create new one
            ['quantity' => $data['quantity']] // update quantity or set new quantity
        );
        return response()->json($item, 201);
    }

    // update a cart item
    public function updateItem(Request $request, $id)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
            'product_id' => 'sometimes|exists:products,id', // optional, can be used to change product
        ]);
        $item = CartItem::where('id', $id)->where('cart_id', Cart::where('user_id', Auth::id())->first()->id)->first();
        if (!$item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }
        $item->update($data);
        return response()->json($item);
    }

    // remove a cart item
    public function removeItem($id)
    {
        $item = CartItem::where('id', $id)->where('cart_id', Cart::where('user_id', Auth::id())->first()->id)->first();
        if (!$item) {
            return response()->json(['message' => 'Cart item not found'], 404);
        }
        $item->delete();
        return response()->json(['message' => 'Item removed from cart']);
    }
}
