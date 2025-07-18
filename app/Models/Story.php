<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Story extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function place(){
        return $this->belongsTo(Place::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('stories')->useDisk('stories');
    }
}
