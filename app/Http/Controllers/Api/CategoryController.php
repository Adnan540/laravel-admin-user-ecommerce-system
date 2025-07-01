<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::with('products')->get();
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        if ($category = Category::where('name', $request->name)->first()) {
            return response()->json(['message' => 'Category already exists'], 409);
        } else {
            $request->merge(['parent_id' => $request->parent_id ?? null]); // Ensure parent_id is set to null if not provided
        }
        $category = Category::create($request->all()); // Create a new category with the validated data
        $successMessage = [
            'message' => 'Category created successfully',
            'category' => $category
        ];

        return response()->json($successMessage, 201);
    }


    public function show(string $id)
    {
        $category = Category::with('products')->find($id);
        return response()->json($category);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category);
    }


    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
