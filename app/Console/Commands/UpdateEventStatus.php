<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class UpdateEventStatus extends Command
{
    protected $signature   = 'events:update-status';
    protected $description = '이벤트 상태를 기간 기준으로 자동 전환 (A타입/B타입 공통)';

    public function handle(): void
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // ── A타입: start_date 기준 상태 전환 ──────────────────────────────
        // 시작일 00시 → upcoming → active
        $aActivated = Event::where('event_type', 'A')
            ->where('status', 'upcoming')
            ->whereDate('start_date', '<=', $today)
            ->update(['status' => 'active', 'updated_at' => $now]);

        // 종료일+1 00시 → active|upcoming → ended
        $aEnded = Event::where('event_type', 'A')
            ->whereIn('status', ['active', 'upcoming'])
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'ended', 'updated_at' => $now]);

        // ── B타입: recruit_start_at 기준 상태 전환 ────────────────────────
        // 모집 시작일 도달 → upcoming → active
        $bActivated = Event::where('event_type', 'B')
            ->where('status', 'upcoming')
            ->where('recruit_start_at', '<=', $now)
            ->update(['status' => 'active', 'updated_at' => $now]);

        // 이벤트 종료일 경과 → active|upcoming → ended
        $bEnded = Event::where('event_type', 'B')
            ->whereIn('status', ['active', 'upcoming'])
            ->whereDate('end_date', '<', $today)
            ->update(['status' => 'ended', 'updated_at' => $now]);

        $this->info("A타입 — active: {$aActivated}건 / ended: {$aEnded}건");
        $this->info("B타입 — active: {$bActivated}건 / ended: {$bEnded}건");
    }
}
