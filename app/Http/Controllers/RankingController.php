<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    public function index()
    {
        $now   = now();
        $year  = $now->year;
        $month = $now->month;

        // 이번 달 개인 순위 (확정 기록 기준)
        $monthly = DB::table('crew.running_logs')
            ->join('users', 'crew.running_logs.user_id', '=', 'users.id')
            ->where('crew.running_logs.is_confirmed', true)
            ->whereYear('crew.running_logs.run_date', $year)
            ->whereMonth('crew.running_logs.run_date', $month)
            ->selectRaw('users.id, users.nickname, SUM(crew.running_logs.distance_km) as total_km, COUNT(*) as run_count')
            ->groupBy('users.id', 'users.nickname')
            ->orderByDesc('total_km')
            ->limit(30)
            ->get();

        // 이번 달 조별 순위
        $groupRanking = DB::table('crew.running_logs')
            ->join('users', 'crew.running_logs.user_id', '=', 'users.id')
            ->join('groups', 'users.group_id', '=', 'groups.id')
            ->where('crew.running_logs.is_confirmed', true)
            ->whereYear('crew.running_logs.run_date', $year)
            ->whereMonth('crew.running_logs.run_date', $month)
            ->whereNotNull('users.group_id')
            ->selectRaw('groups.id, groups.name as group_name, SUM(crew.running_logs.distance_km) as total_km, COUNT(DISTINCT users.id) as member_count')
            ->groupBy('groups.id', 'groups.name')
            ->orderByDesc('total_km')
            ->get();

        // 연간 개인 순위 Top 10
        $yearly = DB::table('crew.running_logs')
            ->join('users', 'crew.running_logs.user_id', '=', 'users.id')
            ->where('crew.running_logs.is_confirmed', true)
            ->whereYear('crew.running_logs.run_date', $year)
            ->selectRaw('users.id, users.nickname, SUM(crew.running_logs.distance_km) as total_km, COUNT(*) as run_count')
            ->groupBy('users.id', 'users.nickname')
            ->orderByDesc('total_km')
            ->limit(10)
            ->get();

        return view('ranking.index', compact('monthly', 'groupRanking', 'yearly', 'year', 'month'));
    }
}
