<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    //fetch all blog posts
    public function posts()
    {
        $posts = Post::with('user')->get();

        return response()->json([
            'posts' => $posts
        ]);
    }
    //fetch all blog paginated posts
    public function p_posts()
    {
        $posts = Post::with('user')->paginate(10);

        return response()->json([
            'posts' => $posts
        ]);
    }


    //get a single blog post
    public function singlePost($id)
    {
        $post = Post::where('id', $id)->with('user')->first();
        return response()->json(['post' => $post]);
    }

    //get multi posts
    public function multiplePosts($id)
    {
        //if id is an array of ids
        if (is_array($id)) {
            $posts = Post::whereIn('id', $id)->with('user')->get();
            return response()->json([
                'posts' => $posts
            ]);
        } else {
            $post = Post::where('id', $id)->with('user')->first();

            return response()->json([
                'post' => $post
            ]);
        }
    }


    //delete A post
    public function deletePost($id)
    {
        $post = Post::where('id', $id)->first();

        if ($post) {
            $delete = $post->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Post successfully deleted'],
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

    //delete multiple posts
    public function deletePosts($ids)
    {
        $user = auth()->user();
        $posts = $user->posts->whereIn('id', $ids)->get();

        if ($posts) {
            $delete = $posts->delete();

            if ($delete) {
                return response()->json(
                    ['message' => 'Posts successfully deleted'],
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



    //create a new post
    public function create(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'description' => 'required|string|min:10|max:200',
            'content' => 'required|string|min:150',
            'cover' => 'required|string|max:300',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $post = $user->posts()->create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'content' => $request->content,
                'cover' => $request->cover,
            ]);

            return response()->json([
                'message' => 'Post Created Successfully',
                'post' => $post,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),

            ], 500);
        }
    }



    //update post
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:75',
            'description' => 'required|string|min:10|max:200',
            'content' => 'required|string|min:150',
            'cover' => 'required|string|max:300',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Invalid inputs',
                'errors' => $validator->errors()
            ], 402);
        }

        try {
            $post = $user->posts()->where('id', $id)->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'content' => $request->content,
                'cover' => $request->cover,
            ]);

            return response()->json([
                'message' => 'Post Updated Successfully',
                'post' => $post,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    //fetch my posts
    public function myPosts()
    {
        $user = auth()->user();
        $posts = $user->posts()->get();

        return response()->json([
            'posts' => $posts
        ]);
    }
}
