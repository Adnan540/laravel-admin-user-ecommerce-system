<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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
    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [ // validate requested data all() means all fields in the request
    //         'name' => 'required|string|max:255',
    //         'description' => 'required|string',
    //         'price' => 'required|numeric|min:0',
    //         'discount_price' => 'nullable|numeric|min:0',
    //         'category_id' => 'required|exists:categories,id',
    //         'weight' => 'required|numeric|min:0',
    //         'weight_unit' => 'required|string|in:kg,g,lb,oz',
    //         'image_url' => 'url',
    //     ]);

    //     //check if validation fails
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     } else {
    //         $product = Product::create($validator->validated()); // Product::create() creates a new product with the validated data
    //         return response()->json([
    //             'message' => 'Product created successfully',
    //             'data' => $product,
    //             'status' => 'success'
    //         ], 201);
    //     }
    // }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string|max:255',
            'description'    => 'required|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lte:price',
            'category_id'    => 'required|exists:categories,id',
            'weight'         => 'required|numeric|min:0',
            'weight_unit'    => 'required|string|in:kg,g,lb,oz',

            // Accept EITHER a file OR a URL
            'image'          => 'required_without:image_url|image|mimes:jpg,jpeg,png,webp|max:4096',
            'image_url'      => 'required_without:image|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Only take the fields you want to persist
        $data = Arr::only($request->all(), [
            'name',
            'description',
            'price',
            'discount_price',
            'category_id',
            'weight',
            'weight_unit'
        ]);

        // If a file was uploaded, store it and save its path to image_url
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public'); // storage/app/public/products/...
            $data['image_url'] = $path; // store relative path in DB
        } else {
            // Otherwise use the provided remote URL
            $data['image_url'] = $request->input('image_url');
        }

        $product = Product::create($data);

        // Build response and include a browsable URL if it's a local path
        $payload = $product->toArray();
        if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
            $payload['image_full_url'] = Storage::disk('public')->url($product->image_url);
        }

        return response()->json([
            'message' => 'Product created successfully',
            'data'    => $payload,
            'status'  => 'success'
        ], 201);
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
