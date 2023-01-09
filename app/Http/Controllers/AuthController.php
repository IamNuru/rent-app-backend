<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    //login page
    public function loginPage()
    {
        $url = "http://localhost::3000/login";
        return redirect()->away($url);
    }
    public function user(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $u = User::where('id', $user->id)->with('properties', 'requests', 'tenants')->first();

            return response()->json([
                'user' => $u,
                'token' => $request->bearerToken(),
            ]);
        }
    }

    public function users()
    {
        $user = auth()->user();
        $users = User::with('posts', 'properties', 'tenants', 'requests')->whereNot('id', $user->id)->get();
        return response()->json(['users' => $users]);
    }

    public function deleteUser($id)
    {
        $user = User::where('id', $id)->firstOrFail();
        if ($user) {
            $user->with('requests', 'posts', 'tenants', 'properties')->delete();
            return response()->json(['message' => 'User Deleted successfully']);
        }
    }


    //register new user
    public function register(Request $request)
    {
        try {
            //validate the form request
            $validator = Validator::make($request->all(), [
                'firstName' => 'string|required|min:2|max:20',
                'lastName' => 'string|required|min:2|max:20',
                'email' => 'required|string|email|unique:users',
                'phoneNumber' => 'nullable|string|min:9|max:13',
                'password' => ['required'],
                'type' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Invalid inputs',
                    'errors' => $validator->errors()
                ], 402);
            }

            $user = User::create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'gender' => $request->gender,
                'type' => $request->type ? 'owner' : 'user',
                'phone_number' => $request->phoneNumber,
                'password' => Hash::make($request->password),
            ]);

            if ($request->type) {
                return response()->json([
                    'message' => 'User account created succesfully',
                    'user' => $user,
                    'token' => $user->createToken('Api Token', ['owner'])->plainTextToken,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User account created succesfully',
                    'user' => $user,
                    'token' => $user->createToken('Api Token', ['user'])->plainTextToken,
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }




    //log in a user
    public function login(Request $request)
    {
        // try {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'message' => 'Email and password do not match our records',
            ], 401);
        }

        $user  = User::with('properties', 'tenants', 'requests', 'posts')->where('email', $request->email)->first();

        if ($user->type === 'admin') {
            return response()->json([
                'message' => 'User succesfully logged in',
                'user' => $user,
                'token' => $user->createToken('Api Token', ['admin'])->plainTextToken,
            ], 200);
        } elseif(($user->type === 'owner')) {
            return response()->json([
                'message' => 'User succesfully logged in',
                'user' => $user,
                'token' => $user->createToken('Api Token', ['owner'])->plainTextToken,
            ], 200);
        }else{
            return response()->json([
                'message' => 'User succesfully logged in',
                'user' => $user,
                'token' => $user->createToken('Api Token', ['user'])->plainTextToken,
            ], 200);
        }
    }


    public function update(Request $request){
        $user = auth()->user();

        //validate the form request
        $validator = Validator::make($request->all(), [
            'firstName' => 'string|required|min:2|max:20',
            'lastName' => 'string|required|min:2|max:20',
            'email' => 'required|string|email',
            'phoneNumber' => 'nullable|string|min:9|max:13',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        $u = $user->update([
            'first_name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'phone_number' => $request->phoneNumber,
        ]);

        if($u){
            return response()->json([
                'message' => 'Profile updated successfully',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Something went wrong',
            ]);
        }
    }

    public function updateProfilePhoto(Request $request){
        $user = auth()->user();

        //validate the form request
        $validator = Validator::make($request->all(), [
            'url' => 'string|required|url|min:4|max:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        $u = $user->update([
            'photo' => $request->url,
        ]);

        if($u){
            return response()->json([
                'message' => 'Profile Photo updated successfully',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Something went wrong',
            ]);
        }
    }
}
