<?php

use App\Console\Commands\AwardEventScores;
use App\Console\Commands\SyncEventParticipants;
use App\Console\Commands\UpdateEventStatus;
use App\Services\SystemLogService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

$scheduleWithSystemLog = function (string $cacheKey, string $label) {
    return [
        'before' => fn () => cache()->put("sched:{$cacheKey}:start", microtime(true), 600),
        'onSuccess' => function () use ($cacheKey, $label) {
            $start = cache()->pull("sched:{$cacheKey}:start");
            SystemLogService::log('scheduler', 'info', "{$label} 성공", [
                'duration_ms' => $start ? round((microtime(true) - $start) * 1000) : null,
            ]);
        },
        'onFailure' => fn () => SystemLogService::error('scheduler', "{$label} 실패"),
    ];
};

// B타입 이벤트 상태 자동 전환 (매일 00:05)
$eventsUpdateStatus = Schedule::command(UpdateEventStatus::class)->dailyAt('00:05');
foreach ($scheduleWithSystemLog('events-update-status', 'events:update-status') as $hook => $callback) {
    $eventsUpdateStatus->{$hook}($callback);
}

// A타입 이벤트 기수 신규 구성원 자동 동기화 (매일 01:00)
$eventsSyncParticipants = Schedule::command(SyncEventParticipants::class)->dailyAt('01:00');
foreach ($scheduleWithSystemLog('events-sync-participants', 'events:sync-participants') as $hook => $callback) {
    $eventsSyncParticipants->{$hook}($callback);
}

// A타입 마일리지 이벤트 자동 점수 부여 (매일 02:00)
$eventsAwardScores = Schedule::command(AwardEventScores::class)->dailyAt('02:00');
foreach ($scheduleWithSystemLog('events-award-scores', 'events:award-scores') as $hook => $callback) {
    $eventsAwardScores->{$hook}($callback);
}
