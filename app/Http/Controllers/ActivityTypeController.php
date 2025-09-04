<?php

namespace App\Http\Controllers;


use App\Helpers\ApiResponse;
use App\Http\Resources\ActivityTypesResource;
use App\Models\ActivityType;
use Illuminate\Http\Request;

class ActivityTypeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $activityTypes=ActivityType::get();

        return ApiResponse::getResponse(ActivityTypesResource::collection($activityTypes),200,"تم ارجاع الانشطة");
    }
}
