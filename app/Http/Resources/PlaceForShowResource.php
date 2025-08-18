<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceForShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id'=>$this->id,
            'name'=>$this->name,
            'address'=>$this->address,
            'description'=>$this->description,
            'images'=>$this->getMedia('places')->map(function($media){
                return[
                    'original'=>$media->getUrl(),
                ];
            }),
            'activites'=>ActivityInPlaceResource::collection($this->whenLoaded('activities')),
            'reviewsCount'=>$this->whenLoaded('reviews',fn()=>$this->reviews->count()),
            'reviewAvarge'=>$this->whenLoaded('reviews',fn()=>round($this->reviews->avg('rating'),1)),
            'stories'=>$this->whenLoaded('stories',fn()=>$this->stories->map(function($story){
                return[
                    'story_id'=>$story->id,
                    'txt'=>$story->txt,
                    'image'=>$story->getMedia('stories')->map(fn ($media) => $media->getUrl()),
                    'user_name'=>$story->user->name,
                ];
            })),
        ];
    }
}
