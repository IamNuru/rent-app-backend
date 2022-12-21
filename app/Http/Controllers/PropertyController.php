<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PropertyController extends Controller
{
    //fetch all properties
    public function properties()
    {
        $properties = Property::where('status', 1)->with('user', 'imageslist')->latest()->get();

        return response()->json([
            'properties' => $properties
        ]);
    }
    //fetch all properties
    public function p_properties()
    {
        $properties = Property::where('status', 1)->with('user', 'imageslist')->latest()->paginate(12);

        return response()->json([
            'properties' => $properties
        ]);
    }

    //get a single property
    public function singleProperty($id)
    {
        $property = Property::where('id', $id)->with('user', 'imageslist')->first();
        return response()->json(['property' => $property]);
    }
    //get a multi property
    public function multipleProperties($id)
    {
        //if id is an array of ids
        $array = explode(",", $id);
        if (count($array) > 1) {
            $properties = Property::whereIn('id', $array)->with('user')->get();
            return response()->json([
                'properties' => $properties
            ]);
        } else {
            $property = Property::where('id', $id)->with('user')->first();

            return response()->json([
                'property' => $property
            ]);
        }
    }

    //delete A property
    public function deleteProperty($id)
    {
        $user = auth()->user();
        $array = explode(",", $id);
        if (is_array($array)) {
            $properties = $user->properties()->whereIn('id', $array)->get();
            if ($properties) {
                $delete = $properties->each->delete();
                if ($delete) {
                    return response()->json(['message' => 'Property successfully deleted'], 200);
                } else {
                    return response()->json(['message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['message' => 'Property Not Found']);
            }
        } else {
            $property = $user->properties()->where('id', $id)->first();
            if ($property) {
                $delete = $property->user()->delete();

                if ($delete) {
                    return response()->json(['message' => 'Property successfully deleted'], 200);
                } else {
                    return response()->json(['message' => 'Something went wrong']);
                }
            } else {
                return response()->json(['message' => 'Property Not Found']);
            }
        }
    }

    //delete multiple properties
    public function deleteProperties($ids)
    {
        $user = auth()->user();
        $properties = $user->properties->whereIn('id', $ids)->get();

        if ($properties) {
            $delete = $properties->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Properties successfully deleted'],
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

    //create a new property
    public function create(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'description' => 'required|string|min:20',
            'price' => 'required|integer|min:0',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 401);
        }
        
        try {
            DB::transaction(function () use ($user, $request) {
                $property = $user->properties()->create([
                    'title' => $request->title,
                    'slug' => Str::slug($request->title),
                    'description' => $request->description,
                    'price' => $request->price,
                    'type' => $request->type,
                    'category' => $request->category,
                    'amenities' => $request->amenities,
                    'addresses' => $request->addresses,
                ]);

                $property->images()->sync($request->images);

                return response()->json([
                    'message' => 'Property Created Successfully',
                    'property' => $property,
                ], 200);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }


    //create a new property
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'description' => 'required|string|min:20',
            'price' => 'required|integer|min:0',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 401);
        }

        try {
            $p = Property::where('id', $id)->first();
            $property = $user->properties()->where('id', $id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'type' => $request->type,
                'category' => $request->category,
                'amenities' => $request->amenities,
                'addresses' => $request->addresses,
            ]);

            $p->images()->sync($request->images);

            return response()->json([
                'message' => 'Property Updated Successfully',
                'property' => $property,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }


    //fetch my properties
    public function myProperties()
    {
        $user = auth()->user();
        $properties = $user->properties()->get();

        return response()->json([
            'properties' => $properties
        ]);
    }
}
