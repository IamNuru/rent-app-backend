<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    //get categories
    public function categories(){
        $categories = Category::get();
        return response()->json([
            'categories' => $categories
        ]);
    }



    //get category  properties
    public function category_properties(){
        $categories = Category::with('properties')->get();
        return response()->json([
            'categories' => $categories
        ]);
    }

    //add category
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:3|max:15|unique:categories',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        $category = Category::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
        ]);

        if($category){

            return response()->json([
                'message' => 'Category Created Succesfully'
            ]);
        }

    }

    //update category
    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:2|max:30',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        $category = Category::where('id', $id)->update([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
        ]);

        return response()->json([
            'message' => 'Category Updated Succesfully'
        ]);

    }

    //delete a category
    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();

        if ($category) {
            $delete = $category->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Category successfully deleted'],
                    200
                );
            } else {
                return response()->json([
                    'message' => 'Something went wrong'
                ]);
            }
        } else {
            return response()->json(
                ['message' => 'Category Not Found']
            );
        }
    }


    //get single category
    public function singleCategory($id){
        $category = Category::where('id', $id)->first();

        return response()->json([
            'category' => $category
        ]);
    }
}
