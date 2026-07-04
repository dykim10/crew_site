<?php

namespace App\Http\Controllers;

use App\Services\CrewStatsService;
use Illuminate\View\View;

class IntroduceController extends Controller
{
    public function __construct(private CrewStatsService $crewStats) {}

    public function index(): View
    {
        $stats = [
            'runners'  => '0',
            'branches' => '0',
            'events'   => '0',
            'total_km' => '0km',
        ];
        $branchCount = 0;

        try {
            $stats = $this->crewStats->getPublicStats();
            $branchCount = $this->crewStats->countActiveBranches();
        } catch (\Throwable) {
            // DB 미연결 등 — 0으로 표시
        }

        return view('introduce.index', compact('stats', 'branchCount'));
    }
}
