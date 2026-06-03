<?php

use App\Console\Commands\AwardEventScores;
use App\Console\Commands\SyncEventParticipants;
use App\Console\Commands\UpdateEventStatus;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// B타입 이벤트 상태 자동 전환 (매일 00:05)
Schedule::command(UpdateEventStatus::class)->dailyAt('00:05');

// A타입 이벤트 기수 신규 구성원 자동 동기화 (매일 01:00)
Schedule::command(SyncEventParticipants::class)->dailyAt('01:00');

// A타입 마일리지 이벤트 자동 점수 부여 (매일 02:00)
Schedule::command(AwardEventScores::class)->dailyAt('02:00');
