<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Validator;

class ContactMessagesController extends Controller
{
    // Fetch all contact messages
    public function index()
    {
        $messages = ContactMessage::Latest()->get(); // Fetch all contact messages
        if (!$messages || $messages->isEmpty()) {
            $failedFetchMessage = [
                'Message' => 'no contact messages found',
                'status' => 'not found'
            ];
            return response()->json($failedFetchMessage, 404);
        } else {
            $successFetchMessage = [
                'Message' => 'Contact messages found',
                'data' => $messages,
                'status' => 'success'
            ];
            return response()->json($successFetchMessage, 200);
        }
    }

    //show contact message by id
    public function show($id)
    {
        $contactMessage = ContactMessage::find($id);
        if (!$contactMessage) {
            $failedMessage = [
                'Message' => 'Contact message not been found',
                'status' => 'not found'
            ];
        } else {
            $successMessage = [
                'Message' => 'Contact message found',
                'data' => $contactMessage,
                'status' => 'success'
            ];
            return response()->json($successMessage, 200);
        }
    }

    // Store a new contact message
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        // Check if validation fails
        if ($validatedData->fails()) {
            return response()->json([
                'Message' => 'Validattion failed',
                'errors' => $validatedData->errors(),
                'status' => 'error'
            ], 422);
        } else {
            // Create a new contact message
            $contactMessage = Contactmessage::create([
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
            ]);
            $successMessage = [
                'Message' => 'Contact Message created successfully',
                'data' => $contactMessage,
                'status' => 'success'
            ];
            return response()->json($successMessage, 201);
        }
    }

    // Update an existing contact message
    public function destroy($id)
    {
        $contactMessage = ContactMessage::find($id);
        if ($contactMessage == null) {
            $failedMessage = [
                'Message' => 'Contact message not been found',
                'status' => 'not found'
            ];
            return response()->json($failedMessage, 404);
        } else {
            $successMessage = [
                'Message' => 'Contact message deleted successfully',
                'data' => $contactMessage,
                'status' => 'success'
            ];
            $contactMessage->delete(); // delete contact message
            return response()->json($successMessage, 200);
        }
    }
    // Update an existing contact message by passing the id
    public function update(Request $request, $id)
    {
        $contactMessage = ContactMessage::find($id);
        if (!$contactMessage) {
            return response()->json([
                'Message' => 'Contact message not been found',
                'status' => 'not found'
            ], 404);
        } else {
            $validatedData = Validator::make($request->all(), [
                'name' => 'required|string',
                'email' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string',
            ]);
            if ($validatedData()->fails()) {
                return response()->json([
                    'Message' => 'Validation fails',
                    'errors' => $validatedData->errors(),
                    'status' => 'error'
                ], 422);
            } else {
                $contactMessage->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'subject' => $request->subject,
                    'message' => $request->message,
                ]);
                $contactMessage->save(); // Save the updated contact message
                // Return success response
                return response()->json([
                    'Message' => 'Contact message updated successfully',
                    'data' => $contactMessage,
                    'status' => 'success'
                ], 200);
            }
        }
    }
}
