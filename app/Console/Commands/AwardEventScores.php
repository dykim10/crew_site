<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\EventScore;
use App\Models\RunningLog;
use App\Models\UsersDetail;
use Illuminate\Console\Command;

class AwardEventScores extends Command
{
    protected $signature   = 'events:award-scores';
    protected $description = 'A타입 마일리지 이벤트 — 목표 달성 시 자동 점수 부여';

    public function handle(): void
    {
        // A타입 + (active|ended) + score_type='mileage_grade' 이벤트 조회
        $events = Event::where('event_type', 'A')
            ->whereIn('status', ['active', 'ended'])
            ->where('score_type', 'mileage_grade')
            ->get();

        $totalProcessed = 0;

        foreach ($events as $event) {
            // score_config 파싱: [{"grade":"A","target_km":120,"reward_score":10}, ...]
            $scoreConfig = $event->score_config ?? [];
            if (empty($scoreConfig)) {
                $this->warn("이벤트 #{$event->id} ({$event->name}) — score_config 없음");
                continue;
            }

            // 해당 이벤트의 모든 event_scores 레코드 조회 (score=0인 것만 대상)
            $eventScores = EventScore::where('event_id', $event->id)
                ->where('score', 0)
                ->get();

            foreach ($eventScores as $eventScore) {
                $userId = $eventScore->user_id;

                // 사용자의 등급(users_detail.grade) 조회
                $userDetail = UsersDetail::where('user_id', $userId)->first();
                if (!$userDetail || !$userDetail->grade) {
                    continue; // 등급 정보 없으면 스킵
                }

                // 해당 등급의 target_km, reward_score 찾기
                $config = collect($scoreConfig)->firstWhere('grade', $userDetail->grade);
                if (!$config) {
                    continue; // 등급에 맞는 설정 없으면 스킵
                }

                $targetKm = $config['target_km'] ?? 0;
                $rewardScore = $config['reward_score'] ?? 0;

                // 사용자의 이벤트 기간 내 confirmed 기록 합산
                $achievedKm = RunningLog::where('user_id', $userId)
                    ->where('is_confirmed', true)
                    ->whereBetween('run_date', [$event->start_date, $event->end_date])
                    ->sum('distance_km');

                // 목표 달성 여부 확인
                if ($achievedKm >= $targetKm) {
                    // event_scores 업데이트
                    $eventScore->update([
                        'score'      => $rewardScore,
                        'source'     => 'auto_mileage',
                        'updated_at' => now(),
                    ]);
                    $totalProcessed++;
                }
            }
        }

        $this->info("마일리지 자동 점수 부여: {$totalProcessed}건 완료");
    }
}
