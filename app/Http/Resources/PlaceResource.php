<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceResource extends JsonResource
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
            'rating'=>$this->whenLoaded('reviews',fn()=>round($this->reviews->avg('rating'),1)),
            'image'=>$this->getFirstMediaUrl('places'),
            'address'=>$this->address,
            'price'=>$this->when(isset($this->cheapest_price),function(){
                return $this->cheapest_price;
            }),
        ];
    }
}
