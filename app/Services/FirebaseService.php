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
    $this->messaging=(new Factory)
    ->withServiceAccount(storage_path('app/firebase/fungo-app-c9239-517bfac48f5f.json'))
    ->createMessaging();
}

public function sendNotificationToAllUserDevices(array $tokens,string $title,string $body,array $data){

    try{
        $notification=Notification::create($title,$body);

        $message=CloudMessage::new()->withNotification($notification)
        ->withData($data);

        $report=$this->messaging->sendMulticast($message,$tokens);
        Log::alert("FCM sent: {$report->successes()->count()} success");
    } catch(\Exception $e){

        Log::error('error while sending FCM notification');
    }
}










}