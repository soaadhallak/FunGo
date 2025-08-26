<?php

use App\Jobs\DeleteExpiredSalesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:notify-expiring-sales')->dailyAt('9:00');
Schedule::job(new DeleteExpiredSalesJob)->daily();