<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;


class AdminProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']);
    }

    // List all products with category
    public function index()
    {
        $products = Product::with('category')->latest()->get();

        return response()->json([
            'message' => 'Products found',
            'data'    => $products, // already includes image_full_url accessor
            'status'  => 'success'
        ], 200);
    }

    // Create
    public function store(Request $request)
    {
        // Normalize empty strings to null so required_without works properly
        $request->merge([
            'image_url' => $request->filled('image_url') ? $request->input('image_url') : null,
        ]);

        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'name_en'        => 'nullable|string|max:255',
            'description'    => 'required|string',
            'description_en' => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lte:price',
            'category_id'    => 'required|exists:categories,id',
            'weight'         => 'required|numeric|min:0',
            'weight_unit'    => 'required|string|in:kg,g,lb,oz',

            // exactly one source: file OR url
            'image'     => 'required_without:image_url|file|image|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'image_url' => 'required_without:image|nullable|url',
        ]);

        // If a file uploaded, store it and override image_url with relative path
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_url'] = $path;
        }

        $product = Product::create(Arr::only($data, [
            'name',
            'name_en',
            'description',
            'description_en',
            'price',
            'discount_price',
            'category_id',
            'weight',
            'weight_unit',
            'image_url'
        ]));

        return response()->json([
            'message' => 'Product created successfully',
            'data'    => $product->fresh(), // has image_full_url accessor
            'status'  => 'success',
        ], 201);
    }


    // Show one
    public function show($id)
    {
        $product = Product::with('category')->find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json(['data' => $product], 200);
    }

    // Update
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->merge([
            'image_url' => $request->filled('image_url') ? $request->input('image_url') : null,
        ]);

        $data = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'name_en'        => 'sometimes|nullable|string|max:255',
            'description'    => 'sometimes|required|string',
            'description_en' => 'sometimes|nullable|string',
            'price'          => 'sometimes|required|numeric|min:0',
            'discount_price' => 'sometimes|nullable|numeric|min:0|lte:price',
            'category_id'    => 'sometimes|required|exists:categories,id',
            'weight'         => 'sometimes|required|numeric|min:0',
            'weight_unit'    => 'sometimes|required|string|in:kg,g,lb,oz',

            // optional on update
            'image'     => 'sometimes|nullable|file|image|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'image_url' => 'sometimes|nullable|url',
        ]);

        // If a new file is uploaded, delete old local file (if any) and store the new one
        if ($request->hasFile('image')) {
            if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
                Storage::disk('public')->delete($product->image_url);
            }
            $data['image_url'] = $request->file('image')->store('products', 'public');
        } elseif (array_key_exists('image_url', $data)) {
            // If image_url is provided (possibly null to clear), just assign it
            // (Do not delete old file automatically when switching to a remote URL unless you want that)
        }

        $product->update($data);

        return response()->json([
            'message' => 'Product updated successfully',
            'data'    => $product->fresh(),
            'status'  => 'success',
        ], 200);
    }


    // Delete
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image_url && !str_starts_with($product->image_url, 'http')) {
            Storage::disk('public')->delete($product->image_url);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully', 'status' => 'success']);
    }
}
