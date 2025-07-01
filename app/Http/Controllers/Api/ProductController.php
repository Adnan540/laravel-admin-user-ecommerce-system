<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;

//CRUD operations for products

class ProductController extends Controller
{
    // index method to list all products with their categories
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json([
            'message' => 'List of products with categories',
            'data' => $products
        ]);
    }


    // store method to create a new product
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',   // Ensure category exists
            'discount_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0 ',
            'weight_unit' => 'nullable|string|max:10',
            'image_url' => 'nullable|url',

        ]);
        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        // Create the product if validation passes
        $product = Product::create($request->all());
        // Return a success response
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

    // show method to display a single product with its category
    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $successMessage = [
            'message' => "product with id $id retrieved successfully",
            'data' => $product
        ];
        return response()->json($successMessage, 200);
    }


    // update method to modify an existing product
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id', // Ensure category exists
            'dicount_price' => 'nullable|numeric|min:0',
            'weight' => 'nullable|numeric|min:0',
            'wieght_unit' => 'nullable|string|max:10',
            'image_url' => 'nullable|url',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update the product if validation passes
        $product->update($request->all());

        // Return a success response
        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    // destroy method to delete a product
    public function destroy(Product $product)
    {
        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Delete the product
        $product->delete();

        // Return a success response
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
