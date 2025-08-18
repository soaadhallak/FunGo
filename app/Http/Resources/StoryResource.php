<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoryResource extends JsonResource
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
            'txt'=>$this->txt ?? null,
            'image'=>$this->whenLoaded('media',fn()=>$this->getFirstMediaUrl('stories')),
            'userName'=>$this->whenLoaded('user',$this->user->name),
            'createdAt'=>$this->created_at
        ];
    }
}
