<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum'); // Ensure the user is authenticated
        $this->middleware('is_admin'); // Ensure the user is an admin
    }

    // Add methods for admin functionalities here
    public function index()
    {
        // Logic to list all admins or perform admin-specific actions
        return response()->json(['message' => 'Admin dashboard accessed successfully']);
    }

    public function show($id)
    {
        // Logic to show a specific admin's details
        return response()->json(['message' => "Admin details for ID: $id"]);
    }

    public function update(Request $request, $id)
    {
        // Logic to update an admin's details
        return response()->json(['message' => "Admin ID: $id updated successfully"]);
    }
    public function destroy($id)
    {
        // Logic to delete an admin
        return response()->json(['message' => "Admin ID: $id deleted successfully"]);
    }

    public function dashboard()
    {
        // Logic for admin dashboard
        return response()->json(['message' => 'Admin dashboard data']);
    }
}
