<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\SalesPlaceResource;
use App\Models\Place;
use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function getSalePlace(Place $place){

       $sales=$place->sales()->latest()->get();

        if($sales->isEmpty()){
            return ApiResponse::getResponse(null,200,'لا يوجد عروض لهذا المكان');
        }
        return ApiResponse::getResponse(SalesPlaceResource::collection($sales),200,'تم عرض العروض');
        
    }

    public function index(){

        $sales=Sale::with('place.media')->latest()->get();
        return ApiResponse::getResponse(SalesPlaceResource::collection($sales),200,'تم عرض العروض');

    }

}
