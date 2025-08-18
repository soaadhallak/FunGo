<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\V1\StoreStoryRequest;
use App\Models\Story;
use App\Http\Resources\StoryResource;
use App\Http\Requests\V1\StoryUpdateRequest;
use App\Models\Place;
use App\Policies\StoryPolicy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StoryController extends Controller
{
     public function store(StoreStoryRequest $request)
    {
        $story=Story::create($request->except('image'));
        if($request->hasFile('image')){
            $story->addMedia($request->file('image'))->toMediaCollection('stories');
        }
        return ApiResponse::getResponse(new StoryResource($story->load(['media','user'])),201,'تمت اضافة القصة بنجاح');

    }

    public function update(StoryUpdateRequest $request, Story $story)
    {
        Gate::authorize('update',$story);
        $story->update($request->except('image'));
        if ($request->hasFile('image')) {
            $story->clearMediaCollection('stories');
            $story->addMediaFromRequest('image')->toMediaCollection('stories');
        }
        return ApiResponse::getResponse(new StoryResource($story->load(['media','user'])),200,'تم تعديل القصة بنجاح');

    }

    public function destroy(Story $story)
    {
        Gate::authorize('delete',$story);
        if($story->media->isNotEmpty()){
            $story->clearMediaCollection('stories');
        }
        $story->delete();
        return ApiResponse::getResponse(null,200,'تم حذف القصة');
    }

    public function getStoryPlace(Place $place){
        $stories=$place->stories()->with(['media','user'])->latest()->get();
        if($stories->isEmpty()){
            return ApiResponse::getResponse(null,404,'لا يوجد قصص زوار بعد');
        }
        return ApiResponse::getResponse(StoryResource::collection($stories),200);
    }


}
