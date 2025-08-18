<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaceActivity extends Model
{
    protected $table='place_activity';

    public $timestamps=false;

    protected $fillable=[
        'place_id',
        'activity_type_id',
        'min_price',
        'max_price'
    ];

    public function activityType(){
        return $this->belongsTo(ActivityType::class);
    }
}
