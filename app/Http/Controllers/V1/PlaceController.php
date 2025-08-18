<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Filters\PlacesFilter;
use App\Http\Resources\PlaceResource;
use App\Models\Place;
use App\Http\Resources\PlaceForShowResource;
use App\Helpers\ApiResponse;

class PlaceController extends Controller
{
    public function index(Request $request)
    {
        $places = (new PlacesFilter($request))->apply();

        return ApiResponse::getResponse(PlaceResource::collection($places),200,'تم ارجاع المكان');

    }

    public function show(Place $place)
    {
        $place->load(['activities','media','reviews','stories.media','stories.user']);
        return ApiResponse::getResponse(new PlaceForShowResource($place),200,'تم ارجاع المكان');
    }
}
