<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventGroup;
use App\Models\Branch;
use App\Models\RunningLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * A타입 이벤트 현황 보드 서비스 (app/Services/EventBoardService.php)
 *
 * A타입 이벤트의 조별·지부별 현황 데이터를 제공한다.
 * 모든 집계는 is_confirmed=true인 러닝 기록만 대상으로 한다.
 */
class EventBoardService
{
    /**
     * 특정 이벤트의 특정 지부 내 조별 집계를 반환한다.
     *
     * @param Event $event A타입 이벤트
     * @param int $branchId 지부 ID
     * @return array [
     *   {
     *     'group_id' => int,
     *     'group_no' => int,
     *     'group_name' => string,
     *     'member_count' => int,
     *     'total_km' => float,
     *     'rank' => int
     *   },
     *   ...
     * ]
     */
    public function getGroupRankings(Event $event, int $branchId): array
    {
        if (!$event->start_date || !$event->end_date) {
            return [];
        }

        $groups = EventGroup::where('event_id', $event->id)
            ->where('branch_id', $branchId)
            ->get();

        $rankings = [];

        foreach ($groups as $group) {
            $memberCount = DB::table('crew.event_group_members')
                ->where('group_id', $group->id)
                ->count();

            $totalKm = (float) DB::table('crew.running_logs')
                ->join('crew.event_group_members', 'crew.running_logs.user_id', '=', 'crew.event_group_members.user_id')
                ->where('crew.event_group_members.group_id', $group->id)
                ->where('crew.running_logs.is_confirmed', true)
                ->whereBetween('crew.running_logs.run_date', [
                    $event->start_date->toDateString(),
                    $event->end_date->toDateString(),
                ])
                ->sum('crew.running_logs.distance_km');

            $rankings[] = [
                'group_id'     => $group->id,
                'group_no'     => $group->group_no,
                'group_name'   => $group->group_name,
                'member_count' => $memberCount,
                'total_km'     => $totalKm,
            ];
        }

        // total_km 기준 내림차순 정렬 후 순위 부여
        usort($rankings, fn ($a, $b) => $b['total_km'] <=> $a['total_km']);
        $rank = 1;
        foreach ($rankings as &$item) {
            $item['rank'] = $rank++;
        }
        unset($item);

        return $rankings;
    }

    /**
     * 이벤트의 전체 지부별 요약을 반환한다.
     *
     * @param Event $event A타입 이벤트
     * @return array [
     *   {
     *     'branch_id' => int,
     *     'branch_name' => string,
     *     'group_count' => int,
     *     'total_members' => int,
     *     'total_km' => float
     *   },
     *   ...
     * ]
     */
    public function getAllBranchSummary(Event $event): array
    {
        if (!$event->start_date || !$event->end_date) {
            return [];
        }

        $branches = Branch::all(['id', 'name']);
        $summary = [];

        foreach ($branches as $branch) {
            $groupCount = EventGroup::where('event_id', $event->id)
                ->where('branch_id', $branch->id)
                ->count();

            if ($groupCount === 0) {
                continue; // 조가 없는 지부는 스킵
            }

            $totalMembers = (int) DB::table('crew.event_groups')
                ->join('crew.event_group_members', 'crew.event_groups.id', '=', 'crew.event_group_members.group_id')
                ->where('crew.event_groups.event_id', $event->id)
                ->where('crew.event_groups.branch_id', $branch->id)
                ->count('crew.event_group_members.id');

            $totalKm = (float) DB::table('crew.running_logs')
                ->join('crew.event_group_members', 'crew.running_logs.user_id', '=', 'crew.event_group_members.user_id')
                ->join('crew.event_groups', 'crew.event_group_members.group_id', '=', 'crew.event_groups.id')
                ->where('crew.event_groups.event_id', $event->id)
                ->where('crew.event_groups.branch_id', $branch->id)
                ->where('crew.running_logs.is_confirmed', true)
                ->whereBetween('crew.running_logs.run_date', [
                    $event->start_date->toDateString(),
                    $event->end_date->toDateString(),
                ])
                ->sum('crew.running_logs.distance_km');

            $summary[] = [
                'branch_id'    => $branch->id,
                'branch_name'  => $branch->name,
                'group_count'  => $groupCount,
                'total_members' => $totalMembers,
                'total_km'     => $totalKm,
            ];
        }

        return $summary;
    }

    /**
     * 특정 조 구성원들의 기간별 러닝 기록 합산을 반환한다.
     *
     * @param Event $event A타입 이벤트
     * @param int $groupId 조 ID
     * @param string|null $from 시작일 (Y-m-d, null이면 이벤트 시작일)
     * @param string|null $to 종료일 (Y-m-d, null이면 이벤트 종료일)
     * @return array [
     *   {
     *     'user_id' => int,
     *     'name' => string,
     *     'confirmed_km' => float,
     *     'pending_km' => float
     *   },
     *   ...
     * ]
     */
    public function getMemberLogs(Event $event, int $groupId, ?string $from = null, ?string $to = null): array
    {
        $from = $from ?? $event->start_date?->toDateString();
        $to = $to ?? $event->end_date?->toDateString();

        if (!$from || !$to) {
            return [];
        }

        // 조 구성원 조회
        $members = DB::table('crew.event_group_members')
            ->join('public.users', 'crew.event_group_members.user_id', '=', 'public.users.id')
            ->where('crew.event_group_members.group_id', $groupId)
            ->select('crew.event_group_members.user_id', 'public.users.name')
            ->get();

        $logs = [];

        foreach ($members as $member) {
            $confirmedKm = (float) RunningLog::where('user_id', $member->user_id)
                ->where('is_confirmed', true)
                ->whereBetween('run_date', [$from, $to])
                ->sum('distance_km');

            $pendingKm = (float) RunningLog::where('user_id', $member->user_id)
                ->where('is_confirmed', false)
                ->whereBetween('run_date', [$from, $to])
                ->sum('distance_km');

            $logs[] = [
                'user_id'      => $member->user_id,
                'name'         => $member->name,
                'confirmed_km' => $confirmedKm,
                'pending_km'   => $pendingKm,
            ];
        }

        return $logs;
    }

    /**
     * 특정 조의 일별 인증 km 합산을 반환한다. (차트 데이터)
     *
     * @param Event $event A타입 이벤트
     * @param int $groupId 조 ID
     * @return array [
     *   {
     *     'date' => 'Y-m-d',
     *     'km' => float
     *   },
     *   ...
     * ]
     */
    public function getDailyStats(Event $event, int $groupId): array
    {
        if (!$event->start_date || !$event->end_date) {
            return [];
        }

        $results = DB::table('crew.running_logs')
            ->select(
                DB::raw("DATE(crew.running_logs.run_date) as date"),
                DB::raw("SUM(crew.running_logs.distance_km) as km")
            )
            ->join('crew.event_group_members', 'crew.running_logs.user_id', '=', 'crew.event_group_members.user_id')
            ->where('crew.event_group_members.group_id', $groupId)
            ->where('crew.running_logs.is_confirmed', true)
            ->whereBetween('crew.running_logs.run_date', [
                $event->start_date->toDateString(),
                $event->end_date->toDateString(),
            ])
            ->groupBy(DB::raw("DATE(crew.running_logs.run_date)"))
            ->orderBy('date')
            ->get();

        return $results
            ->map(fn ($row) => [
                'date' => $row->date,
                'km'   => (float) $row->km,
            ])
            ->toArray();
    }
}
