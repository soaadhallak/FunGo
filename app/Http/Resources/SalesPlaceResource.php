<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesPlaceResource extends JsonResource
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
            'title'=>$this->title,
            'startDate'=>$this->date_start,
            'endDate'=>$this->date_end,
            'placeId'=>$this->place_id,
            'placeName'=>$this->place->name,
            'image'=>$this->place->getFirstMediaUrl('places')
        ];
    }
}
