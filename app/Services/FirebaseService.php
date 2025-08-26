<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FirebaseService{

protected $messaging;

public function __construct()
{
    $factory=(new Factory)->withServiceAccount(storage_path('app/firebase/fungo-app-c9239-firebase-adminsdk-fbsvc-d70d218135.json'));
    $this->messaging=$factory->createMessaging();
}

public function sendNotificationToAllUserDevices(array $tokens,string $title,string $body,array $data){

    try{
        $message = CloudMessage::new()
            ->withNotification(Notification::create($title, $body))
            ->withData($data);

       
       $report=$this->messaging->sendMulticast($message,$tokens);

       logger("FCM sent: {$report->successes()->count()} success, {$report->failures()->count()} failed.");
        foreach ($report->failures()->getItems() as $failure) {
           logger('FCM failure reason: ' . $failure->error()->getMessage());
       }
    } catch(\Exception $e){

        Log::error('error while sending FCM notification');
    }
}











}