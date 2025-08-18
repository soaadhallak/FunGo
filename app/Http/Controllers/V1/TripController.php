<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreNewTripRequest;
use App\Helpers\ApiResponse;
use App\Http\Requests\V1\GetTripForUserRequest;
use APP\Http\Resources\TripPlaceResource;
use App\Models\Place;
use App\Models\Trip;

class TripController extends Controller
{
    public function addToTrip(StoreNewTripRequest $request){
    
    $user=$request->user();
     // جلب أو إنشاء رحلة
    $trip=$user->trips()->latest()->firstOrCreate([]);

    if(!$trip){
        $trip=$user->trips()->create();
    }
     // منع التكرار
    if($trip->places()->where('place_id',$request->place_id)->exists()){
        return ApiResponse::getResponse(null,409,"هذا المكان مضاف الى الرحلة سابقا");
    }
      // الإضافة
    $trip->places()->attach($request->place_id);

    return ApiResponse::getResponse(null,201,'تمت الاضافة إلى رحلتي');
    
   }

   public function getTripForUser(GetTripForUserRequest $request){
      $user=$request->user();
      $lng=$request->longitude;
      $lat=$request->latitude;
      $trip=$user->trips()->latest()->with(['places'])->first();
      if(!$trip){
       return ApiResponse::getResponse(null,200,"لم تقم بانشاء رحلة بعد ");
      }
      $places=$trip->places()->select('places.*')->
     selectRaw('ST_Distance_Sphere(POINT(longitude,latitude),POINT(?,?)) as distance',[$lng,$lat])
     ->orderBy('distance')->with('media')->get();

     return ApiResponse::getResponse(TripPlaceResource::collection($places),200,"Places in your trip ordered by distance.");
   }

   public function destroy(Trip $trip){
    $trip->delete();
    return ApiResponse::getResponse(null,200,"تم حذف الرحلة");
   }

   public function deletePlace(Place $place,Request $request){
    $user=$request->user();
    $trip=$user->trips()->latest()->first();
    $trip->places()->detach($place->id);
    return ApiResponse::getResponse(null,200,"تم حذف المكان من الرحلة");
   }
}
