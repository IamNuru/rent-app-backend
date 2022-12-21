<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\TenantController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'user' => $request->user()->with('properties','tenants','requests')
    ]);
});*/
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()->delete();
    return response()->json([
        'message' => 'User Logged out successfully'
    ]);
}); 


//un-auth api
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::get('/properties', [PropertyController::class, 'properties']);
Route::get('/posts', [PostController::class, 'posts']);
Route::get('/requests', [RequestController::class, 'requests']);
//get single resource
Route::get('/property/{id}', [PropertyController::class, 'singleProperty']);
Route::get('/request/{id}/{slug}', [RequestController::class, 'singleRequest']);
Route::get('/post/{id}', [PostController::class, 'singlePost']);
//get multiple resource
Route::get('/properties/multi/{id}', [PropertyController::class, 'multipleProperties']);
Route::get('/requests/multi/{id}', [RequestController::class, 'multipleRequests']);
Route::get('/posts/multi/{id}', [PostController::class, 'multiplePosts']);

//paginated results
/******* The following routes are paginated routes
 * ***********
 */

Route::get('/p_properties', [PropertyController::class, 'p_properties']);
Route::get('/p_posts', [PostController::class, 'p_posts']);
Route::get('/p_requests', [RequestController::class, 'p_requests']);

/******** end 
 * *******
 */


//auth api
Route::middleware('auth:sanctum')->group(function (){
    Route::post('/message', [ChatController::class, 'message']);
    Route::get('/messages/{sender_id}', [ChatController::class, 'messages']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [AuthController::class, 'users']);
    Route::patch('/auth/update', [AuthController::class, 'update']);
    Route::patch('/auth/update-profile-photo', [AuthController::class, 'updateProfilePhoto']);
    Route::delete('/user/{id}', [AuthController::class, 'deleteUser']);
    Route::middleware(['ability:owner,admin'])->group(function(){
        Route::post('/property', [PropertyController::class, 'create']);
        Route::put('/property/{id}', [PropertyController::class, 'update']);
        Route::delete('/property/{id}', [PropertyController::class, 'deleteProperty']);
        Route::delete('/properties/{ids}', [PropertyController::class, 'deleteProperties']);
        
        Route::post('/tenant', [TenantController::class, 'create']);
        Route::put('/tenant/{id}', [TenantController::class, 'update']);
        Route::delete('/tenant/{id}', [TenantController::class, 'deleteTenant']);
        Route::get('/tenant/{id}', [TenantController::class, 'singleTenant']);
    
        Route::get('/my/tenants', [TenantController::class, 'myTenants']);
        Route::get('/my/properties', [PropertyController::class, 'myProperties']);
        Route::get('/my/posts', [PostController::class, 'myPosts']);
        Route::get('/my/requests', [RequestController::class, 'myRequests']);
    });
    Route::middleware(['abilities:admin'])->group(function(){
        Route::post('/post', [PostController::class, 'create']);
        Route::put('/post/{id}', [PostController::class, 'update']);
        Route::delete('/post/{id}', [PostController::class, 'deletePost']);
        Route::delete('/posts/{ids}', [PostController::class, 'deletePosts']);
    });
    
    Route::post('/request', [RequestController::class, 'create']);
    Route::put('/request/{id}', [RequestController::class, 'update']);
    Route::delete('/request/{id}', [RequestController::class, 'deleteRequest']);
    Route::delete('/requests/{ids}', [RequestController::class, 'deleteRequests']);
});
