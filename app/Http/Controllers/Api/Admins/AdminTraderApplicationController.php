<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraderApplication;
use Illuminate\Support\Facades\Validator;

class AdminTraderApplicationController extends Controller
{
    public function __construct()
    {
        $this->middleware((['auth:sanctum', 'is_admin'])); //ensure only authenticated admin can access this controller
    }

    public function index()
    {
        $TraderData = TraderApplication::latest()->get(); // get by latest
        return response()->json($TraderData, 200);
    }
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'business_name' => 'required|string',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'Message' => 'failed to validate the data',
                'error' => $validatedData->errors()
            ], 422);
        } else {
            $data = TraderApplication::create($validatedData->validated());
            $successMessage = [
                'Message' => 'your new data has been created',
                'Data' => $data,
                'Status' => 'succeed'
            ];
            return response()->json($successMessage, 200);
        }
    }

    public function show($id)
    {
        $data = TraderApplication::find($id); // Find trader application by ID
        if ($data == null) {
            return response()->json([
                'Message' => 'Trader application has not been found',
            ], 404);
        } else {
            $successMessage = [
                'Message' => 'Trader application has not been found',
                'Data' => $data,
                'Status' => 'succeed'
            ];
            return response()->json([
                $successMessage,
            ])->setStatusCode(200, 'Trader application has been found');
        }
    }

    public function update(Request $request, $id)
    {
        $data = TraderApplication::find($id);

        if (!$data) {
            return response()->json([
                'Message' => 'Trader application has been deleted or not found',
            ], 404);
        }

        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
        ]);

        if ($validatedData->fails()) {
            return response()->json([
                'Message' => 'Failed to validate data',
                'errors' => $validatedData->errors(),
            ], 422);
        }

        $data->update($validatedData->validated());

        return response()->json([
            'Message' => 'Trader application has been updated',
            'Data' => $data,
            'Status' => 'succeed',
        ], 200);
    }

    public function delete($id)
    {
        $data = TraderApplication::find($id);
        if (!$data) {
            return response()->json([
                'Message' => 'Trader application has not been found',
            ], 404);
        } else {
            $data->delete();
            return response()->json([
                'Message' => 'Trader application has been deleted',
                'Status' => 'succeed'
            ])->setStatusCode(200, 'Trader application has been deleted successfully');
        }
    }
}
