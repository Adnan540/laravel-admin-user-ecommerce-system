<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kitchen;
use Illuminate\Support\Facades\Storage;

class AdminKitchenController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']);
    }

    // List all kitchen items
    public function index()
    {
        $kitchen = Kitchen::latest()->get();

        return response()->json([
            'message' => $kitchen->isEmpty() ? 'No kitchen items found' : 'Kitchen items found',
            'data'    => $kitchen, // each item includes image_full_url from accessor
            'status'  => 'success',
        ], 200);
    }

    // Create
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'title_en'        => 'nullable|string|max:255',
            'description'     => 'nullable|string',
            'description_en'  => 'nullable|string',
            'price'           => 'required|numeric|min:0',

            // one of these two must be provided
            'image'           => 'required_without:image_url|image|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'image_url'       => 'required_without:image|url',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('kitchen', 'public');
            $data['image'] = $path; // store relative path in DB
        } else {
            $data['image'] = $request->input('image_url');
        }

        unset($data['image_url']);

        $item = Kitchen::create($data);

        return response()->json([
            'message' => 'Kitchen item created successfully',
            'data'    => $item, // accessor adds image_full_url automatically
            'status'  => 'success',
        ], 201);
    }

    // Show one
    public function show(string $id)
    {
        $item = Kitchen::find($id);
        if (!$item) {
            return response()->json(['message' => 'Kitchen item not found'], 404);
        }

        return response()->json([
            'message' => 'Kitchen item found',
            'data'    => $item,
            'status'  => 'success',
        ], 200);
    }

    // Update
    public function update(Request $request, string $id)
    {
        $item = Kitchen::find($id);
        if (!$item) {
            return response()->json(['message' => 'Kitchen item not found'], 404);
        }

        $data = $request->validate([
            'title'           => 'sometimes|required|string|max:255',
            'title_en'        => 'sometimes|nullable|string|max:255',
            'description'     => 'sometimes|nullable|string',
            'description_en'  => 'sometimes|nullable|string',
            'price'           => 'sometimes|required|numeric|min:0',
            'image'           => 'sometimes|nullable|image|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'image_url'       => 'sometimes|nullable|url',
        ]);

        if ($request->hasFile('image')) {
            if ($item->image && !str_starts_with($item->image, 'http')) {
                Storage::disk('public')->delete($item->image);
            }
            $path = $request->file('image')->store('kitchen', 'public');
            $data['image'] = $path;
        } elseif ($request->filled('image_url')) {
            $data['image'] = $request->input('image_url');
        }

        unset($data['image_url']);

        $item->update($data);

        return response()->json([
            'message' => 'Kitchen item updated successfully',
            'data'    => $item->fresh(), // includes image_full_url
            'status'  => 'success',
        ], 200);
    }

    // Delete
    public function destroy(string $id)
    {
        $item = Kitchen::find($id);
        if (!$item) {
            return response()->json(['message' => 'Kitchen item not found'], 404);
        }

        if ($item->image && !str_starts_with($item->image, 'http')) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete();

        return response()->json([
            'message' => 'Kitchen item deleted successfully',
            'status'  => 'success',
        ], 200);
    }
}
