<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_members'  => DB::table('users')->count(),
            'total_km'       => (float) DB::table('crew.running_logs')->sum('distance_km'),
            'monthly_logs'   => DB::table('crew.running_logs')
                ->whereYear('run_date', now()->year)
                ->whereMonth('run_date', now()->month)
                ->count(),
            'active_events'  => DB::table('crew.events')
                ->where('status', 'active')
                ->whereDate('end_date', '>=', now()->toDateString())
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
