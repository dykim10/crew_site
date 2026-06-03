<?php

use App\Console\Commands\UpdateEventStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// B타입 이벤트 상태 자동 전환 (매일 00:05)
Schedule::command(UpdateEventStatus::class)->dailyAt('00:05');
