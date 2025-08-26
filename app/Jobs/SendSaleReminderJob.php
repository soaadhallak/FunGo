<?php

namespace App\Jobs;

use App\Models\DeviceToken;
use App\Models\Sale;
use App\Services\FirebaseService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;

class SendSaleReminderJob implements ShouldQueue
{
    use Queueable,Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Sale $sale)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebase): void
    {
        $title = 'لا ضيع الفرصة';
        $body = "باقي يومين لينتهي العرض في {$this->sale->place->name} لحق حالك";
        $data = ['sale_id' => $this->sale->id,
        'place_name' => $this->sale->place->name,
        'place_id'=>$this->sale->place->id];
        $tokens=DeviceToken::pluck('token')->toArray();
        
        $firebase->sendNotificationToAllUserDevices($tokens,$title,$body,$data);

    }
}
