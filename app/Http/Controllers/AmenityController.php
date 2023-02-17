<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    //get amenities
    public function amenities(){
        $amenities = Amenity::get();
        return response()->json([
            'amenities' => $amenities
        ]);
    }



    //get amenity  properties
    public function amenity_properties(){
        $amenities = Amenity::with('properties')->get();
        return response()->json([
            'amenities' => $amenities
        ]);
    }

    //add amenity
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:30|unique:amenities',
        ]);

        $amenity = Amenity::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Amenity Created Succesfully'
        ]);

    }

    //update amenity
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:30',
        ]);

        $amenity = Amenity::where('id', $id)->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Amenity Updated Succesfully'
        ]);

    }

    //delete a amenity
    public function destroy($id)
    {
        $amenity = Amenity::where('id', $id)->first();

        if ($amenity) {
            $delete = $amenity->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Amenity successfully deleted'],
                    200
                );
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        } else {
            return response()->json(
                ['message' => 'Amenity Not Found']
            );
        }
    }


    //get single amenity
    public function singleAmenity($id){
        $amenity = Amenity::where('id', $id)->first();

        return response()->json([
            'amenity' => $amenity
        ]);
    }
}
