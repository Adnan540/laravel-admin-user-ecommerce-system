<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;

class AdminCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']);
    }

    // Show all categories with their parent and children
    public function index()
    {
        $categories = Category::with('parent', 'children')->latest()->get();
        return response()->json([
            'message' => 'list of categories',
            'data' => $categories,
            'status' => 'success'
        ], 200);
    }

    // add a new category
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'parent_id' => 'nullable' | 'exists:categories,id',
            'description' => 'nullable|string',
        ]);
        //check if validation fails
        if ($validator->fails()) {
            $failedResponse = [
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 'error'
            ];
            return response()->json($failedResponse, 422);
        } else {
            // If validation passes, create the category
            $successResponse = [
                'message' => 'Cagtegory has been added successfully',
                'data' => Category::create($validator->validated()),
                'status' => 'success'
            ];
            return response()->json($successResponse, 201);
        }
    }

    // show categotry by id
    public function show($id)
    {
        $category = Category::with('parent', 'children')->find($id);
        if (!$category) {
            return response()->json([
                'message' => 'Category not been found',
                'status' => 'error'
            ]);
        } else {
            $successResponse = [
                'message' => 'Category found',
                'data' => $category,
                'status' => 'success'
            ];
            return response()->json($successResponse);
        }
    }

    // update category by passing id

}
