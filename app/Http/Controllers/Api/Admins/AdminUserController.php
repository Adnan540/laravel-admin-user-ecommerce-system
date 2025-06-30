<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']);
    }
    //get all users
    public function index()
    {
        $user = User::with('cart', 'wishlist', 'orders', 'payments', 'shippingAddress',)->latest()->get();

        if (!$user) {
            $failedMessage = [
                'Message' => 'user not been found',
                'status' => 'not found'
            ];
            return response()->json([$failedMessage,], 404);
        } else {
            return response()->json([
                'Message' => 'user found',
                'data' => $user,
                'status' => 'user has been found'
            ], 200);
        }
    }

    //get single user
    public function show($id)
    {
        $user = User::with('cart', 'whishlist', 'orders', 'payments', 'shippingAddress')->find($id);

        if (!$user) {
            $failedMessage = [
                'Message' => 'User not found',
                'status' => 'not found'
            ];
            return response()->json([$failedMessage], 404);
        } else {
            $successMessage = [
                'Message' => 'User found',
                'data' => $user,
                'status' => 'user has been found'
            ];
            return response()->json($successMessage, 200);
        }
    }

    //update user
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'Message' => 'User not been found',
                'Status' => 'not found'
            ], 404);
        } else {
            $user->update($request->all()); // Update user with all request data
            $user->save();

            return response()->json([
                'Message' => 'User updated successfully',
                'data' => $user,
                'Status' => 'user has been updated successfully'
            ]);
        }
    }

    //delete user
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'Message' => 'User not found',
                'Status' => 'Not found',
            ], 404);
        } else {
            $user->delete(); // Delete the user
            $deleteMessage = [
                'Message' => 'User has been deleted successfully',
                'Status' => 'user has been deleted'
            ];
            return response()->json($deleteMessage, 200);
        }
    }

    //add a new user 
    // Add a new user
    public function store(Request $request)
    {
        // ✅ 1. Validate the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            // Add more fields if needed
        ]);

        // ✅ 2. Create the user securely
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // ✅ 3. Return a proper response
        return response()->json([
            'message' => 'User created successfully',
            'status' => 'success',
            'data' => $user
        ], 201);
    }

    //get user by email
    public function getUserByEmail($email)
    {
        // Validate the email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'Message' => 'Invalid email format',
                'Status' => 'error'
            ], 400);
        }

        $user = User::where('email', $email)->first(); // Find the user by email
        if ($user == null) {
            return response()->json([
                'Message' => 'User not been found',
                'Status' => 'not found'
            ], 404);
        } else {
            return response()->json([
                'Message' => 'User found',
                'data' => $user,
                'Status' => 'user has been found'
            ], 200);
        }
    }
}
