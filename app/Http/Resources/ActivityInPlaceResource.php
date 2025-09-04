<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityInPlaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $priceAverage=($this->pivot->min_price + $this->pivot->max_price) / 2;
       return[
            'id'=>$this->id,
            'name'=>$this->name,
            'minPrice'=>$this->pivot->min_price,
            'maxPrice'=>$this->pivot->max_price,
            'priceAverage'=>$priceAverage,
        ];
    }
}
