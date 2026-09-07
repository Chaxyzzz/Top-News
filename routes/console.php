<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// TopNews Scheduled Article Publisher (Every Minute)
Schedule::command('topnews:publish-scheduled')->everyMinute();

// TopNews Analytics Rollup & Reconciliation (Daily at Midnight)
Schedule::command('topnews:analytics-rollup')->dailyAt('00:05');
