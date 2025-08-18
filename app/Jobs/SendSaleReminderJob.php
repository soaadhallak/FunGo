<?php

namespace App\Jobs;

use App\Models\Sale;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendSaleReminderJob implements ShouldQueue
{
    use Queueable,Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $delayInSecond=0;
        $targetDate=Carbon::now()->addDays(2)->startOfDay();
        $sales=Sale::with(['place:id,name'])->
        whereDate('date_end',$targetDate)->whereNull('notified_at')->get();
        foreach($sales as $sale){
            $updated=Sale::where('id',$sale->id)->whereNull('notified_at')->update(['notified_at'=>now()]);
            if($updated){
                SendSaleNotificationJob::dispatch($sale)->delay(now()->addSeconds($delayInSecond));
                $delayInSecond+=120;
            }
        }
        Log::alert("Queued reminders for expiring sales.");

    }
}
