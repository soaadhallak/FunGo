<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
       protected $fillable=[
        'user_id',
        'token'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
