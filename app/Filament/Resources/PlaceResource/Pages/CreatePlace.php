<?php

namespace App\Filament\Resources\PlaceResource\Pages;

use App\Events\PlaceCreated;
use App\Filament\Resources\PlaceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePlace extends CreateRecord
{
    protected static string $resource = PlaceResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return null;
    }

   protected function afterCreate(): void
    {
      $place=$this->record;  
      $title='اضافة مكان جديد';
      $body='اكتشف الان'. $place->name;
      $data=['place_id' => $place->id];  
        event(new PlaceCreated($place,$title,$body,$data));
    }
    
  

}
