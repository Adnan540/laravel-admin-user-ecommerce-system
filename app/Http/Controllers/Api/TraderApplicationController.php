<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TraderApplication;
use Illuminate\Support\Facades\Validator;


//CRUD operations for Trader Application
// This controller handles the CRUD operations for trader applications, including validation and response formatting.
class TraderApplicationController extends Controller
{
    public function index()
    {
        return TraderApplication::get(); // Fetch all trader applications by latest
    }

    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'phone' => 'sometimes|required|string|max:15',
            'business_name' => 'sometimes|required|string|max:255',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'state' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:20',
            'country' => 'sometimes|required|string|max:100',
        ]);
        if ($validatedData->fails()) {
            return response()->json([
                'Message' => 'Validation Error',
                'Errors' => $validatedData->errors(),
                "application_status" => 'pending'
            ], 422);
        } else {
            $data = TraderApplication::create($validatedData->validated());
            $succressMessage = [
                'Message' => 'Trader Application Created Successfully',
                'Data' => $data,
                'application_status' => 'pending'
            ];
            return response()->json([$succressMessage], 201);
        }
    }
    //show by passing id
    public function show($id)
    {
        $TraderApplication = TraderApplication::find($id); // Find trader application by ID
        if (!$TraderApplication) {
            return response()->json([
                'Message' => 'Trader Application Not Found',
            ], 404);
        } else {
            return response()->json([
                'Message' => 'Trader Application Found',
                'Data' => $TraderApplication
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $traderApplication = TraderApplication::find($id);
        if ($traderApplication == null) {
            return response()->json([
                'Message' => 'Trader Application Not Found',
            ], 404);
        } else {
            $validatedData = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:15',
                'business_name' => 'sometimes|required|string|max:255',
                'address' => 'sometimes|required|string|max:255',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'sometimes|required|string|max:100',
                'postal_code' => 'sometimes|required|string|max:20',
                'country' => 'sometimes|required|string|max:100',
            ]);
            if ($validatedData->fails()) {
                $failedMessage = [
                    'application_status' => 'pending',
                    'Message' => 'Validation Error',
                    'Errors' => $validatedData->errors()
                ];
                return response()->json([$failedMessage], 404);
            } else {
                $traderApplication->update($validatedData->validated());
                $traderApplication->save();

                // Return success message with updated data
                $successMessage = [
                    'Message' => 'Trader Application Updated Successfully',
                    'Data' => $traderApplication
                ];
                return response()->json([$successMessage], 200);
            }
        }
    }

    public function destroy($id)
    {
        $data = TraderApplication::find($id);
        if (!$data) {
            return response()->json([
                'Message' => 'Trader Application Not Found',
            ], 404);
        } else {
            $data->delete();
            $deleteMessage = [
                'Message' => 'trafder Application Deleted Successfully',
                'Data' => $data
            ];
            return response()->json($deleteMessage, 200);
        }
    }
}
