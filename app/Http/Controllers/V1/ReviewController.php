<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\StoreReviewRequest;
use App\Http\Requests\V1\UpdateReviewRequest;
use App\Models\Review;
use App\Http\Resources\ReviewResource;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request)
    {
        $data=$request->all();
        $review=Review::create($data);
        return ApiResponse::getResponse(new ReviewResource($review->load(['user','place','activityType'])),201,'تم اضافة تقييم');
    }

     public function update(UpdateReviewRequest $request,Review $review)
    {
        Gate::authorize('update',$review);
        $review->update($request->validated());
        return ApiResponse::getResponse(new ReviewResource($review->load(['user','place','activityType'])),200,'تم تعديل التقييم');
    }

    public function destroy(Review $review)
    {
        Gate::authorize('delete',$review);
        $review->delete();
        return ApiResponse::getResponse(null,200,'تم حذف التقييم');
    }
    /*
    public function getAllReviewsForPlace(Place $place){

        $reviews=$place->reviews()->with(['user','activityType'])->latest()->get();
         if($reviews->isEmpty()){
            return ApiResponse::getResponse(null,404,'لا يوجد تقييمات بعد');
        }
        return ApiResponse::getResponse(ReviewResource::collection($reviews),200);
    }*/
}
