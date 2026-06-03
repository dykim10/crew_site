<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEventStatus extends Command
{
    protected $signature   = 'events:update-status';
    protected $description = 'B타입 이벤트 상태를 모집기간·이벤트기간 기준으로 자동 전환';

    public function handle(): void
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // ① 모집 시작일 도달 → upcoming → active
        $activated = Event::where('event_type', 'B')
            ->where('status', 'upcoming')
            ->where('recruit_start_at', '<=', $now)
            ->update(['status' => 'active']);

        // ② 이벤트 종료일 경과 → active|upcoming → ended
        $ended = Event::where('event_type', 'B')
            ->whereIn('status', ['active', 'upcoming'])
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'ended']);

        $this->info("이벤트 상태 자동 전환 완료: active {$activated}건 / ended {$ended}건");
    }
}
