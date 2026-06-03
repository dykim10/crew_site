<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\EventGroup;
use App\Models\EventScore;
use App\Models\RunningLog;
use App\Models\User;
use App\Models\UsersDetail;
use Illuminate\Support\Collection;

/**
 * A타입 이벤트 엑셀 다운로드 (app/Exports/EventGroupExport.php)
 *
 * 다운로드 컬럼:
 *   - 참가자명
 *   - 지부명
 *   - 조명
 *   - 총달성km
 *   - 지부내인증km합산순위(rank)
 *   - 개인획득점수
 */
class EventGroupExport extends BaseExport
{
    /**
     * @param Event $event
     * @param int|null $branchId  지부별 필터 (null이면 전체)
     */
    public function __construct(
        private Event $event,
        private ?int $branchId = null,
    ) {
        // 데이터 로드
        $data = $this->loadData();
        parent::__construct(collect($data));
    }

    public function title(): string
    {
        return 'A타입_' . ($this->branchId ? '지부별' : '전체');
    }

    public function headings(): array
    {
        return [
            '참가자명',
            '지부명',
            '조명',
            '총달성km',
            '지부내순위',
            '개인점수',
        ];
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['branch_name'],
            $row['group_name'],
            $row['achieved_km'],
            $row['rank'],
            $row['score'],
        ];
    }

    /**
     * 데이터 로드 로직
     *
     * 1. 이벤트에 속한 EventGroup 조회 (branch_id 필터 적용 시)
     * 2. 각 그룹의 구성원 조회
     * 3. 사용자별 이벤트 기간 내 총 달성km 계산
     * 4. 지부별 랭킹 계산
     * 5. event_scores 에서 점수 조회
     */
    private function loadData(): array
    {
        // A타입 검증
        if (!$this->event->isTypeA()) {
            return [];
        }

        // EventGroup 조회 (branch_id 필터)
        $groupQuery = EventGroup::where('event_id', $this->event->id);
        if ($this->branchId) {
            $groupQuery->where('branch_id', $this->branchId);
        }
        $groups = $groupQuery->with('branch', 'members')->get();

        $rows = [];
        $branchAchievements = []; // 지부별 달성km 집계 (순위 계산용)

        foreach ($groups as $group) {
            $groupId = $group->id;
            $branchId = $group->branch_id;
            $branchName = $group->branch ? $group->branch->name : '미지정';
            $groupName = $group->group_name ?? "그룹 #{$groupId}";

            // 그룹 구성원 user_id 목록
            $memberUserIds = $group->members->pluck('user_id')->toArray();

            foreach ($memberUserIds as $userId) {
                // 사용자 정보 조회
                $user = User::find($userId);
                if (!$user) continue;

                // 이벤트 기간 내 confirmed 기록의 총 달성km
                $achievedKm = RunningLog::where('user_id', $userId)
                    ->where('is_confirmed', true)
                    ->whereBetween('run_date', [$this->event->start_date, $this->event->end_date])
                    ->sum('distance_km');

                // event_scores 에서 점수 조회
                $eventScore = EventScore::where('event_id', $this->event->id)
                    ->where('user_id', $userId)
                    ->first();
                $score = $eventScore ? (float) $eventScore->score : 0;

                // 지부별 달성km 기록 (순위 계산용)
                if (!isset($branchAchievements[$branchId])) {
                    $branchAchievements[$branchId] = [];
                }
                $branchAchievements[$branchId][] = [
                    'user_id'     => $userId,
                    'achieved_km' => $achievedKm,
                ];

                $rows[] = [
                    'name'        => $user->name,
                    'branch_id'   => $branchId,
                    'branch_name' => $branchName,
                    'group_name'  => $groupName,
                    'user_id'     => $userId,
                    'achieved_km' => $achievedKm,
                    'score'       => $score,
                    'rank'        => null, // 나중에 계산
                ];
            }
        }

        // 지부별 랭킹 계산
        $rankMap = []; // [branchId][userId] => rank
        foreach ($branchAchievements as $branchId => $members) {
            // 달성km 기준 내림차순 정렬
            usort($members, fn ($a, $b) => $b['achieved_km'] <=> $a['achieved_km']);

            foreach ($members as $rank => $member) {
                if (!isset($rankMap[$branchId])) {
                    $rankMap[$branchId] = [];
                }
                $rankMap[$branchId][$member['user_id']] = $rank + 1;
            }
        }

        // 순위 정보 주입
        foreach ($rows as &$row) {
            $branchId = $row['branch_id'];
            $userId = $row['user_id'];
            $row['rank'] = $rankMap[$branchId][$userId] ?? '-';
            unset($row['user_id'], $row['branch_id']); // 불필요한 필드 제거
        }

        return $rows;
    }
}
