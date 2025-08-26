<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Filters\PlacesFilter;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use App\Http\Resources\PlaceForShowResource;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\DB;

class PlaceController extends Controller
{
       public function index(Request $request)
{
    $places = Place::with(['activities', 'sales', 'reviews'])
        ->withAvg('reviews as avg_rating', 'rating')
        ->when($request->search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        })
        ->when($request->governorate, function ($query, $governorate) {
            $query->where('governorate', $governorate);
        })
        ->when($request->activity_type_id, function ($query, $activityId) {
            $query->whereHas('activities', function ($q) use ($activityId) {
                $q->where('activity_type_id', $activityId);
            });
        });

    // فلترة حسب الأقرب
    if (
        $request->has('filters') &&
        in_array('nearest', (array)$request->filters) &&
        $request->latitude && $request->longitude
    ) {
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $places = $places->selectRaw('places.*, 
            (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
            cos(radians(longitude) - radians(?)) + 
            sin(radians(?)) * sin(radians(latitude)))) AS distance', 
            [$latitude, $longitude, $latitude]
        )
        ->orderBy('distance');
    }

    // فلترة حسب الأرخص
    if (
        $request->has('filters') &&
        in_array('cheapest', (array)$request->filters)
    ) {
        $activityTypeId = $request->activity_type_id;

        // إذا تم تحديد نوع نشاط
        if ($activityTypeId) {
            $places = $places->leftJoin('place_activity', function ($join) use ($activityTypeId) {
                    $join->on('places.id', '=', 'place_activity.place_id')
                         ->where('place_activity.activity_type_id', $activityTypeId);
                })
                ->addSelect('place_activity.min_price')
                ->orderBy('place_activity.min_price');
        } else {
            // في حال لم يتم تحديد نوع نشاط
            $places = $places->leftJoin('place_activity', 'places.id', '=', 'place_activity.place_id')
                ->addSelect(DB::raw('MIN(place_activity.min_price) as min_price'))
                ->groupBy('places.id')
                ->orderBy('min_price');
        }
    }

    // فلترة حسب وجود العروض
    if (
        $request->has('filters') &&
        in_array('offers', (array)$request->filters)
    ) {
        $places = $places->whereHas('sales');
    }

    // فلترة حسب التقييم
    if (
        $request->has('filters') &&
        in_array('rating', (array)$request->filters)
    ) {
        $places = $places->orderByDesc('avg_rating');
    }

     return ApiResponse::getResponse(PlaceResource::collection($places->get()),200,'تم ارجاع الأماكن');
}

    public function show(Place $place)
    {
        $place->load(['activities','media','reviews','stories.media','stories.user']);
        return ApiResponse::getResponse(new PlaceForShowResource($place),200,'تم ارجاع المكان');
    }


}
