<?php

namespace App\Listeners;

use App\Events\PlaceCreated;
use App\Events\SaleCreated;
use App\Models\DeviceToken;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\FirebaseService;

class SendNotification
{
    /**
     * Create the event listener.
     */
   // protected $firebase;
    public function __construct()
    {
 
    }

    /**
     * Handle the event.
     */
    public function handle(PlaceCreated|SaleCreated $event): void
    {
       $tokens=DeviceToken::pluck('token')->toArray();
       $title=$event->title;
       $body=$event->body;
       $data=$event->data;
        // Send to all users
        $firebase=new FirebaseService();
      $firebase->sendNotificationToAllUserDevices($tokens,$title,$body,$data);
    }
}
