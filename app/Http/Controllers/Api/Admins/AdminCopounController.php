<?php

namespace App\Http\Controllers\Api\Admins;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Validator;

class AdminCopounController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'is_admin']);
    }

    //get all copouns
    public function index()
    {
        $coupon = Coupon::Latest()->get();
        if (!$coupon) {
            return response()->json([
                'message' => 'Copoun not found',
                'status' => 'error'
            ], 404);
        } else {
            $successMessage = [
                'Message' => 'Copoun found',
                'data' => $coupon,
                'status' => 'Success'
            ];
            return response()->json([$successMessage], 200);
        }
    }

    //get single copoun
    public function show($id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Copoun not found',
                'status' => 'error'
            ], 404);
        } else {
            $successMessage = [
                'Message' => 'Copoun found',
                'data' => $coupon,
                'status' => 'Success'
            ];
            return response()->json([$successMessage], 200);
        }
    }
    //update copoun
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            return response()->json([
                'message' => 'Coupon not found',
                'status' => 'error'
            ], 404);
        } else {
            $coupon->update($request->all());
            $coupon->save();
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'code' => 'required|string|max:255',
                'discount' => 'required|numeric|min:0',
                'type' => 'required|string|in:percentage,amount',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'required|boolean',
            ]);
            $data = $validator->validated();
            return response()->json([
                'message' => 'Coupon updated successfully',
                'data' => $data,
                'status' => 'success'
            ]);
        }
    }

    //add copoun
    public function store(Request $request)
    {
        //validate requested data
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255',
            'discount_percent' => 'required|numeric|min:0',
            'expire_at' => 'required|date_format:Y-m-d',
            'max_uses' => 'required|integer|min:1',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            $failedResponse = [
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 'error'
            ];
            return response()->json([$failedResponse], 422);
        } else {
            $copoun = Coupon::create($validator->validated()); // Create a new copoun with the validated data
            return response()->json([
                'message' => 'Copoun created successfully',
                'data' => $copoun,
                'status' => 'success'
            ], 201);
        }
    }

    //delete copoun
    public function destroy($id)
    {
        $copoun = Coupon::find($id);
        if ($copoun == null) {
            return response()->json([
                'message' => 'Copoun not found',
                'status' => 'error'
            ], 404);
        } else {
            $copoun->delete(); // Delete the copoun
            return response()->json([
                'message' => 'Copoun has been deleted successfully',
                'status' => 'success'
            ]);
        }
    }
}
