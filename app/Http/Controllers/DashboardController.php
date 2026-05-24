<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;

/**
 * 대시보드 컨트롤러 (app/Http/Controllers/DashboardController.php)
 *
 * 로그인 직후 진입하는 메인 화면을 담당한다.
 * 직접 로직 없이 DashboardService 에 전부 위임하고 결과를 뷰로 전달한다.
 *
 * 라우트: GET /dashboard → resources/views/dashboard.blade.php
 *
 * 뷰에 전달하는 데이터
 *   stats      : 이번달 거리·목표달성률·누적거리·이벤트점수·조 순위
 *   notices    : 최근 공지사항 3건
 *   mileage    : 진행 중 이벤트 마일리지 달성률 + 남은 일수
 *   events     : 진행 중 이벤트 목록 (최대 3건)
 *   recentLogs : 최근 러닝 기록 3건
 */
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
