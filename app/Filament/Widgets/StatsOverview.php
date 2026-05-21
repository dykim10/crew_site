<?php

namespace App\Filament\Widgets;

use App\Models\Notice;
use App\Models\RunningLog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now        = Carbon::now();
        $thisMonth  = $now->month;
        $thisYear   = $now->year;

        $totalMembers = User::count();

        $totalKmThisMonth = RunningLog::whereYear('run_date', $thisYear)
            ->whereMonth('run_date', $thisMonth)
            ->sum('distance_km');

        $logsThisMonth = RunningLog::whereYear('run_date', $thisYear)
            ->whereMonth('run_date', $thisMonth)
            ->count();

        $pendingLogs = RunningLog::where('is_confirmed', false)->count();

        return [
            Stat::make('전체 회원', number_format($totalMembers) . '명')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make($thisMonth . '월 누적 거리', number_format($totalKmThisMonth, 1) . ' km')
                ->icon('heroicon-o-map')
                ->color('success'),

            Stat::make($thisMonth . '월 러닝 기록', number_format($logsThisMonth) . '건')
                ->icon('heroicon-o-chart-bar')
                ->color('info'),

            Stat::make('미확인 기록', number_format($pendingLogs) . '건')
                ->icon('heroicon-o-clock')
                ->color($pendingLogs > 0 ? 'warning' : 'success'),
        ];
    }
}
