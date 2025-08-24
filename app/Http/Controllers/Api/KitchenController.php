<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kitchen;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Logic to retrieve and return a list of kitchen items
        return response()->json(['message' => 'List of kitchen items']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Logic to validate and store a new kitchen item
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'price' => 'nullable|numeric',
        ]);

        // Assuming Kitchen is a model that handles the kitchen items
        $kitchenItem = Kitchen::create($request->all());

        return response()->json(['message' => 'Kitchen item created successfully', 'item' => $kitchenItem], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Logic to validate and update an existing kitchen item
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image',
            'price' => 'nullable|numeric',
        ]);

        $kitchenItem = Kitchen::findOrFail($id);
        $kitchenItem->update($request->all());

        return response()->json(['message' => 'Kitchen item updated successfully', 'item' => $kitchenItem]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Logic to delete a kitchen item
        $kitchenItem = Kitchen::findOrFail($id);
        $kitchenItem->delete();

        return response()->json(['message' => 'Kitchen item deleted successfully']);
    }
}
