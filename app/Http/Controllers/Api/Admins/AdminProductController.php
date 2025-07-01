<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Product;
use Illuminate\Support\Facades\Validator;

/* 
 * AdminProductController handles product management for admins.
 * It includes methods to list, create, update, and show products.
 * All actions are protected by authentication and admin checks.
 */

class AdminProductController extends Controller
{
    //auth:sanctum => Checks if the request comes from an authenticated user
    //is_admin => Checks if the logged-in user is actually an admin (not just any user)
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']); //middleware act as security checkpoint to ensure only admins can access these routes
    }


    // Show all products with their categories
    public function index()
    {
        $products = Product::with('category')->latest()->get(); //get all product with their categories , ordered by latest first
        return response()->json(['data' => $products], 200);
    }

    // add a new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [ // validate requested data all() means all fields in the request
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'required|numeric|min:0',
            'weight_unit' => 'required|string|in:kg,g,lb,oz',
            'image_url' => 'url',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        } else {
            $product = Product::create($validator->validated()); // Product::create() creates a new product with the validated data
            return response()->json([
                'message' => 'Product created successfully',
                'data' => $product,
                'status' => 'success'
            ], 201);
        }
    }

    // Show a specific product by ID
    public function show($id)
    {
        $product = Product::with('category')->find($id); //find product by id with its category
        if (!$product) { //if product not found
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json(['data' => $product], 200); //return product data
    }

    // Update a product by passing the ID
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['Message' => 'product not found']);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'weight' => 'required|numeric|min:0',
            'weight_unit' => 'required|string|in:kg,g,lb,oz',
            'image_url' => 'required|url',
        ]);
        if ($product->update($data)) {
            return response()->json([
                'message' => 'Product updated successfully',
                'data' => $product,
                'status' => 'success'
            ], 200);
        } else {
            return response()->json([
                'message' => 'failed to update product',
                'status' => 'error'
            ]);
        }
    }

    // Delete a product by ID
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) { // check if product exists
            return response()->json(['message' => 'Product not found'], 404);
        } else {
            $product->delete();
            return response()->json(['message' => 'Product has been deleted successfully ', 'status' => 200]);
        }
    }
}
