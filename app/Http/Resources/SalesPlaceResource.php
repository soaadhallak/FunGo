<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        $now   = Carbon::now()->startOfDay();
        $start = Carbon::parse($this->date_start)->startOfDay();
        $end   = Carbon::parse($this->date_end)->startOfDay();

        if ($now->lt($start)) {
            $days = $now->diffInDays($start); 
            $remaining = "يبدأ بعد {$days} يوم";
        } else {
            $days = $now->diffInDays($end);  
            $remaining = "ينتهي بعد {$days} يوم";
        }
        return[
            'id'=>$this->id,
            'title'=>$this->title,
            'body'=>$this->body,
            //'startDate'=>$this->date_start,
            //'endDate'=>$this->date_end,
            //'daysLeft' => max(0, (int) Carbon::now()->diffInDays(Carbon::parse($this->date_end), false)),
            'remaining_days'  => $remaining,
            'placeId'=>$this->place_id,
            //'placeName'=>$this->place->name,
            'image'=>$this->place->getFirstMediaUrl('places'),
            'placegovernorate'=>$this->place->governorate,
        ];
    }
}
