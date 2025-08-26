<?php

namespace App\Console\Commands;

use App\Jobs\SendSaleReminderJob;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyExpiringSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-expiring-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $delayInSecond=0;
        $targetDate=Carbon::now()->addDays(2)->startOfDay();
        $sales=Sale::with(['place:id,name'])->
        whereDate('date_end',$targetDate)->whereNull('notified_at')->get();
        foreach($sales as $sale){
            $updated=Sale::where('id',$sale->id)->whereNull('notified_at')->update(['notified_at'=>now()]);
            if($updated){
                SendSaleReminderJob::dispatch($sale)->delay(now()->addSeconds($delayInSecond));
                $delayInSecond+=120;
            }
        }
        $this->info("Queued reminders for expiring sales.");
    }
}
