<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $service) {}

    public function index(Request $request)
    {
        $user = $request->user();

        return view('dashboard', [
            'stats'      => $this->service->getStats($user),
            'notices'    => $this->service->getNotices($user),
            'mileage'    => $this->service->getMileageProgress($user),
            'events'     => $this->service->getActiveEvents($user),
            'recentLogs' => $this->service->getRecentLogs($user, 3),
        ]);
    }
}
