<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $category = Category::with('products')->get();
        return response()->json($category);
    }

    public function store(Request $request) {}


    public function show(string $id)
    {
        $category = Category::with('products')->find($id);
        return response()->json($category);
    }

    public function update(Request $request, string $id)
    {
        //
    }


    public function destroy(string $id)
    {
        //
    }
}
