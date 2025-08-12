<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// تنظيف المواعيد المعلقة كل دقيقة
Schedule::command('appointments:cleanup-pending')->hourly()
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/appointments-cleanup.log'));
