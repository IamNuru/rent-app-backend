<?php

namespace App\Http\Controllers;

use App\Models\Support;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupportController extends Controller
{
    public function create(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|min:2|max:20',
            'email' => 'required|string|email|max:30',
            'message' => 'required|string|min:10|max:1500',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs: Sorry check inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $contact = Support::create([
                'firstname' => $request->firstName,
                'email' => $request->email,
                'message' => $request->message,
                'phone_number' => $request->phone,
            ]);

            return response()->json([
                'message' => 'Message Sent. Our Support team would respond shortly',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }
}
