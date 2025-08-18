<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\FavouritesResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(){
        $user=auth()->user();
        $favorites=$user->favoritePlaces()->with(['media'])->latest()->get();
        return ApiResponse::getResponse(FavouritesResource::collection($favorites),200,'المفضلة');
    }

   public function store(Place $place){
        $user=auth()->user();
        if($user->favoritePlaces()->where('place_id',$place->id)->exists()){
            $user->favoritePlaces()->detach($place->id);
            return ApiResponse::getResponse(null,200,'تم إزالة المكان من المفضلة');
        }
        $user->favoritePlaces()->attach($place->id);
        return ApiResponse::getResponse(null,200,'تم إضافة المكان إلى المفضلة');
    }

    public function destroy(Place $place){
        $user=auth()->user();
        $user->favoritePlaces()->detach($place->id);
        return ApiResponse::getResponse(null,200,'تم إزالة المكان من المفضلة');

    }

}
