<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'comment'=>$this->comment,
            'rating'=>$this->rating,
            'user'=>$this->whenLoaded('user',fn()=>$this->user->name),
            'place'=>$this->whenLoaded('place',fn()=>$this->place->name),
            'activity_type'=>$this->whenLoaded('activityType',fn()=>$this->activityType->name)
        ];
    }
}
