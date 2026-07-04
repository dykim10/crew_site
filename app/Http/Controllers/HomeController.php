<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Branch;
use App\Models\Event;
use App\Models\Notice;
use App\Models\PhotoGallery;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 스킨: 로그인 사용자 DB → 기본값 _skin_v1
        $skinClass = '_skin_v1';
        if (auth()->check()) {
            $detail = auth()->user()->detail;
            if ($detail && $detail->skin_select) {
                $skinClass = $detail->skin_select;
            }
        }
        $activeTheme = ($skinClass === '_skin_v2') ? 'v2' : 'v1';

        // 메인 노출용 B타입 이벤트: active → upcoming 순, 최대 4개
        $events = Event::where('event_type', 'B')
            ->whereIn('status', ['active', 'upcoming'])
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'upcoming' THEN 1 ELSE 2 END")
            ->orderBy('start_date')
            ->limit(4)
            ->get();

        $notices = collect();
        $freePosts = collect();
        $qnaPosts = collect();
        $photoGalleries = collect();
        $branches = collect();

        try {
            $notices = Notice::where('target_type', 'all')
                ->orderByDesc('is_pinned')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $freePosts = Board::where('board_type', 'free')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $qnaPosts = Board::where('board_type', 'qna')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();

            $photoGalleries = PhotoGallery::orderByDesc('taken_at')
                ->orderByDesc('sort_order')
                ->limit(5)
                ->get();

            $branches = Branch::where('status', 'active')
                ->orderBy('name')
                ->get();
        } catch (\Throwable) {
            // 테스트/DB 미연결 시 홈은 목업 없이 빈 목록
        }

        return view("home.{$activeTheme}", compact(
            'skinClass',
            'activeTheme',
            'events',
            'notices',
            'freePosts',
            'qnaPosts',
            'photoGalleries',
            'branches',
        ));
    }

    public function switchTheme(Request $request)
    {
        $user = auth()->user();

        abort_unless(
            $user && in_array($user->role, ['super_admin', 'region_admin']),
            403
        );

        $validated = $request->validate(['theme' => 'required|in:v1,v2']);

        Setting::set('active_theme', $validated['theme']);

        return redirect()->route('home');
    }
}
