<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    //fetch all tenants
    public function tenants()
    {
        $tenants = Tenant::with('user')->get();

        return response()->json([
            'tenants' => $tenants
        ]);
    }

    //get a single tenant
    public function singleTenant($id)
    {
        $tenant = Tenant::where('id', $id)->with('user')->first();
        return response()->json(['tenant' => $tenant]);
    }


    //fetch my tenants
    public function myTenants()
    {
        $user = auth() -> user();
        $tenants = $user->tenants()->get();

        return response()->json([
            'tenants' => $tenants
        ]);
    }


    //Add tenant
    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'firstName' => 'string|required|min:2|max:20',
            'lastName' => 'string|required|min:2|max:20',
            'phoneNumber' => 'nullable|string|min:9|max:13',
            'occupation' => 'nullable|string|max:50',
            'photo' => 'nullable|string|max:300',
            'gender' => 'nullable|string',
            'status' => 'nullable|string',
            'email' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $tenant = $user->tenants()->create([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'occupation' => $request->occupation,
                'photo' => $request->photo,
                'gender' => $request->gender,
                'status' => $request->status,
                'email' => $request->email,
                'owing' => $request->owing,
            ]);

            return response()->json([
                'message' => 'Tenant Created Successfully',
                'tenant' => $tenant,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }

    //update tenant
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'firstName' => 'string|required|min:2|max:20',
            'lastName' => 'string|required|min:2|max:20',
            'phoneNumber' => 'nullable|string|min:9|max:13',
            'occupation' => 'nullable|string|max:50',
            'photo' => 'nullable|string|max:300',
            'gender' => 'nullable|string',
            'status' => 'nullable|string',
            'email' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $tenant = $user->tenants()->where('id', $id)->update([
                'first_name' => $request->firstName,
                'last_name' => $request->lastName,
                'phone_number' => $request->phoneNumber,
                'occupation' => $request->occupation,
                'photo' => $request->photo,
                'gender' => $request->gender,
                'status' => $request->status,
                'email' => $request->email,
                'owing' => $request->owing,
            ]);

            return response()->json(['message' => 'Tenant Updated Successfully', 'tenant' => $tenant,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }


    public function deleteTenant($id){
        $tenant = Tenant::where('id',$id)->firstOrFail();
        $tenant->delete();

        return response()->json(['message' => 'Tenant Deleted Successfully']);
        
    }

    
}
