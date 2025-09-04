<?php

use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\V1\DeviceTokenController;
use App\Http\Controllers\V1\FavoriteController;
use App\Http\Controllers\V1\UserController;
use App\Http\Controllers\V1\PlaceController;
use App\Http\Controllers\V1\ReviewController;
use App\Http\Controllers\V1\SaleController;
use App\Http\Controllers\V1\StoryController;
use App\Http\Controllers\V1\TripController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//route for place controller
Route::prefix('/places')->middleware('auth:sanctum')->group(function(){

    Route::get('index',[PlaceController::class,'index']);

    Route::get('{place}/show',[PlaceController::class,'show']);
});

  //route for authentication
  Route::prefix('/users')->group(function(){
    Route::post('register',[UserController::class,'register']);
    Route::post('login',[UserController::class,'login']);
    Route::post('logout',[UserController::class,'logout'])->middleware('auth:sanctum');
  });
    
    //route to register device token
    Route::post('/device-token',DeviceTokenController::class)->middleware('auth:sanctum');


    //route for trip
    Route::prefix('/trips')->middleware('auth:sanctum')->group(function(){

    Route::post('add-trip',[TripController::class,'addToTrip']);

    Route::post('my-trip',[TripController::class,'getTripForUser']);
    
    Route::delete('my-trip/{place}/place',[TripController::class,'deletePlace']);

    Route::delete('my-trip/{trip}',[TripController::class,'destroy']);
    });

    Route::prefix('/story')->middleware(['auth:sanctum'])->group(function(){

        Route::post('store',[StoryController::class,'store']);
        Route::delete('{story}/destroy',[StoryController::class,'destroy']);
        Route::post('{story}/update',[StoryController::class,'update']);
        Route::get('{place}/getStoryPlace',[StoryController::class,'getStoryPlace']);
    });

    Route::prefix('/sale')->middleware(['auth:sanctum'])->group(function(){
      
      Route::get('{place}/getSalesPlace',[SaleController::class,'getSalePlace']);
      Route::get('index',[SaleController::class,'index']);
    });
    Route::prefix('review')->middleware(['auth:sanctum'])->group(function(){
        Route::post('store',[ReviewController::class,'store']);
        Route::delete('{review}/delete',[ReviewController::class,'destroy']);
        Route::post('{review}/update',[ReviewController::class,'update']);
        //Route::get('{place}/getAll',[ReviewController::class,'getAllReviewsForPlace']);
    });
    Route::prefix('/favorites')->middleware(['auth:sanctum'])->group(function(){
        Route::get('index',[FavoriteController::class,'index']);
        Route::post('{place}/store',[FavoriteController::class,'store']);
        Route::delete('{place}/delete',[FavoriteController::class,'destroy']);
    });

     Route::get('/activity-type',ActivityTypeController::class)->middleware('auth:sanctum');
