<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventScore;
use App\Models\Generation;
use App\Models\UserGeneration;
use Illuminate\Console\Command;

class SyncEventParticipants extends Command
{
    protected $signature   = 'events:sync-participants';
    protected $description = 'A타입 이벤트 — 기수 신규 가입 구성원을 EventScore에 자동 추가';

    public function handle(): void
    {
        $events = Event::where('event_type', 'A')
            ->whereIn('status', ['upcoming', 'active'])
            ->whereNotNull('generation')
            ->get();

        $total = 0;

        foreach ($events as $event) {
            $gen = Generation::where('number', $event->generation)->first();
            if (!$gen) continue;

            $userIds = UserGeneration::where('generation_id', $gen->id)->pluck('user_id');

            foreach ($userIds as $userId) {
                $score = EventScore::firstOrCreate(
                    ['event_id' => $event->id, 'user_id' => $userId],
                    ['score' => 0, 'source' => 'auto_generation']
                );
                if ($score->wasRecentlyCreated) $total++;
            }

            $this->line("  [{$event->name}] {$gen->display_name} 동기화 완료");
        }

        $this->info("참여자 동기화 완료: 신규 {$total}명 추가");
    }
}
