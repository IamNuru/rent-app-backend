<?php

namespace App\Http\Controllers;

use App\Models\Request as ModelsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RequestController extends Controller
{

    //fetch all requests
    public function requests()
    {
        $requests = ModelsRequest::with('user')->get();

        return response()->json([
            'requests' => $requests
        ]);
    }

    //fetch all paginated requests
    public function p_requests()
    {
        $requests = ModelsRequest::with('user')->paginate(2);

        return response()->json([
            'requests' => $requests
        ]);
    }

    //get a single request
    public function singleRequest($id, $slug)
    {
        $request = ModelsRequest::where('id', $id)->with('user')->first();
        return response()->json(['request' => $request]);
    }

    
    //get multi request
    public function multipleRequests($id)
    {
        //if id is an array of ids
        $array = explode(",", $id);
        if (count($array) > 1) {
            $requests = ModelsRequest::whereIn('id', $array)->with('user')->get();
            return response()->json([
                'requests' => $requests
            ]);
        } else {
            $request = ModelsRequest::where('id', $id)->with('user')->first();

            return response()->json([
                'request' => $request
            ]);
        }
    }

    //delete A request
    public function deleteRequest($id)
    {
        $request = ModelsRequest::where('id', $id)->first();

        if ($request) {
            $delete = $request->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Request successfully deleted'],
                    200
                );
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        } else {
            return response()->json(
                ['message' => 'Request Not Found']
            );
        }
    }

    //delete multiple requests
    public function deleteRequests($ids)
    {
        $user = auth()->user();
        $requests = $user->requests()->whereIn('id', $ids)->get();

        if ($requests) {
            $delete = $requests->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Requests successfully deleted'],
                    200
                );
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        } else {
            return response()->json(
                ['message' => 'Post Not Found']
            );
        }
    }


    //create a new request
    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'message' => 'required|string|min:10|max:1500',
            'type' => 'required|string',
            'category' => 'required|string|max:20',
            'min_price' => 'required|integer|min:1',
            'max_price' => 'required|integer|min:1|gte:min_price',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs: Sorry check inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $request = $user->requests()->create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'message' => $request->message,
                'type' => $request->type,
                'addresses' => json_encode($request->addresses),
                'amenities' => json_encode($request->amenities),
                'category' => $request->category,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'phone_number' => $request->phoneNumber,
            ]);

            return response()->json([
                'message' => 'Request Created Successfully',
                'request' => $request,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }


    //create a new request
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'message' => 'required|string|min:10|max:150',
            'type' => 'required|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 401);
        }

        try {
            $request = $user->request->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'message' => $request->message,
                'type' => $request->type,
            ]);

            return response()->json([
                'message' => 'Request Created Successfully',
                'request' => $request,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }


    //fetch my requests
    public function myRequests()
    {
        $user = auth()->user();
        $requests = $user->requests()->get();

        return response()->json([
            'requests' => $requests
        ]);
    }
}
