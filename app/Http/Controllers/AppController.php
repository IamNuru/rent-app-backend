<?php

namespace App\Http\Controllers;

use App\Models\Region;
use App\Models\RegionDistrict;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AppController extends Controller
{
    //get regions
    public function regions(){
        $regions = Region::get();

        return response()->json([
            'regions' => $regions,
        ]);
    }

    //get regions properties
    public function regionProperties(){
        $regions = Region::with('properties')->get();
        
        return response()->json([
            'regions' => $regions,
        ]);
    }

    //add region
    public function createRegion(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:30|unique:regions',
            'latitude' => 'nullable|integer',
            'longitude' => 'nullable|integer',
        ]);

        $region = Region::create([
            'name' => $request->name,
            'longitude' => $request->longitude,
            'latitude' => $request->name->latitude,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'message' => 'Category Created Succesfully'
        ]);
    }

    //get districts 
    public function district(){
        $districts = RegionDistrict::get();
        
        return response()->json([
            'districts' => $districts,
        ]);
    }


    //get districts properties
    public function districtProperties(){
        $districts = RegionDistrict::with('properties')->get();
        
        return response()->json([
            'districts' => $districts,
        ]);
    }
}
