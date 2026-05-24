<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\RunningLog;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * 대시보드 서비스 (app/Services/DashboardService.php)
 *
 * 대시보드 화면에 필요한 데이터를 수집·가공해 반환한다.
 * 단순 조회는 Eloquent scope 를 사용하고,
 * 복잡한 집계·JOIN 은 DB::table() raw 쿼리를 직접 사용한다.
 *
 * [제공 메서드]
 *   getStats(user)           : 수치 카드 4개 데이터
 *                              - 이번달 거리 + 목표 대비 달성률(%)
 *                              - 누적 총 거리 + 총 횟수
 *                              - 진행 중 이벤트 점수
 *                              - 같은 그룹 내 이번달 거리 기준 순위
 *   getNotices(user)         : 최근 공지사항 3건
 *   getMileageProgress(user) : 진행 중 이벤트 마일리지 달성률 + 남은 일수
 *   getActiveEvents(user)    : 현재 날짜 기준 진행 중 이벤트 최대 3건
 *   getRecentLogs(user, N)   : 최근 러닝 기록 N건 (기본값 3)
 */
class DashboardService
{
    // ① 수치 카드 4개 데이터
    public function getStats(User $user): array
    {
        $now   = now();
        $year  = $now->year;
        $month = $now->month;

        // 이번 달 거리 (확정 기록만)
        $monthlyKm = (float) DB::table('crew.running_logs')
            ->where('user_id', $user->id)
            ->where('is_confirmed', true)
            ->whereYear('run_date', $year)
            ->whereMonth('run_date', $month)
            ->sum('distance_km');

        // 이번 달 목표 (user_goals)
        $monthlyGoal = DB::table('crew.user_goals')
            ->where('user_id', $user->id)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        $monthlyPercent = null;
        if ($monthlyGoal && $monthlyGoal->target_km > 0) {
            $monthlyPercent = min(100, round($monthlyKm / $monthlyGoal->target_km * 100));
        }

        // 누적 거리 + 횟수 (확정 기록만)
        $totalRow = DB::table('crew.running_logs')
            ->where('user_id', $user->id)
            ->where('is_confirmed', true)
            ->selectRaw('SUM(distance_km) as total_km, COUNT(*) as total_count')
            ->first();

        // 이벤트 점수 (현재 진행 중 이벤트 합산)
        $activeEvent = DB::table('crew.events')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->orderByDesc('created_at')
            ->first();

        $eventScore = 0;
        if ($activeEvent) {
            $eventScore = (int) DB::table('crew.event_scores')
                ->where('event_id', $activeEvent->id)
                ->where('user_id', $user->id)
                ->sum('score');
        }

        // 조 순위 (같은 group_id 내 이번달 거리 기준 RANK)
        $groupRank  = null;
        $groupTotal = null;
        $groupName  = null;
        if ($user->group_id) {
            $groupMembers = DB::table('crew.running_logs')
                ->join('users', 'crew.running_logs.user_id', '=', 'users.id')
                ->where('users.group_id', $user->group_id)
                ->where('crew.running_logs.is_confirmed', true)
                ->whereYear('crew.running_logs.run_date', $year)
                ->whereMonth('crew.running_logs.run_date', $month)
                ->selectRaw('crew.running_logs.user_id, SUM(crew.running_logs.distance_km) as total_km')
                ->groupBy('crew.running_logs.user_id')
                ->orderByDesc('total_km')
                ->get();

            foreach ($groupMembers as $i => $m) {
                if ((int) $m->user_id === (int) $user->id) {
                    $groupRank = $i + 1;
                    break;
                }
            }

            $groupTotal = DB::table('users')->where('group_id', $user->group_id)->count();
        }

        return [
            'monthly_km'      => $monthlyKm,
            'monthly_percent' => $monthlyPercent,
            'total_km'        => (float) ($totalRow->total_km ?? 0),
            'total_count'     => (int) ($totalRow->total_count ?? 0),
            'event_score'     => $eventScore,
            'has_active_event'=> $activeEvent !== null,
            'group_rank'      => $groupRank,
            'group_total'     => $groupTotal,
        ];
    }

    // ③ 공지사항
    public function getNotices(User $user): Collection
    {
        return Notice::forUser($user, 3);
    }

    // ④ 마일리지 진행바
    public function getMileageProgress(User $user): ?array
    {
        $now = now();

        $event = DB::table('crew.events')
            ->where('status', 'active')
            ->whereNotNull('target_km')
            ->whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->orderByDesc('created_at')
            ->first();

        if (!$event) return null;

        $achieved = (float) DB::table('crew.running_logs')
            ->where('user_id', $user->id)
            ->where('is_confirmed', true)
            ->whereDate('run_date', '>=', $event->start_date)
            ->whereDate('run_date', '<=', $event->end_date)
            ->sum('distance_km');

        $target   = (float) $event->target_km;
        $percent  = $target > 0 ? min(100, round($achieved / $target * 100)) : 0;
        $daysLeft = (int) now()->diffInDays($event->end_date, false);

        return [
            'name'      => $event->name,
            'achieved'  => $achieved,
            'target'    => $target,
            'percent'   => $percent,
            'days_left' => $daysLeft,
            'done'      => $achieved >= $target,
        ];
    }

    // ④ 진행 중 이벤트 목록
    public function getActiveEvents(User $user): Collection
    {
        $now = now();
        return DB::table('crew.events')
            ->where('status', 'active')
            ->whereDate('start_date', '<=', $now->toDateString())
            ->whereDate('end_date', '>=', $now->toDateString())
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();
    }

    // ⑤ 최근 러닝 기록 (확정·미확정 모두 포함, 뷰에서 시각 구분)
    public function getRecentLogs(User $user, int $limit = 3): Collection
    {
        return RunningLog::byUser($user->id)
            ->orderByDesc('run_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
