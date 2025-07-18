<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
       use HasFactory;



    public function place(){
        return $this->belongsTo(Place::class);
    }
    public function activityType(){
        return $this->belongsTo(ActivityType::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
}
