<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $activeTheme = Setting::get('active_theme', 'v1');

        // 메인 노출용 B타입 이벤트: active → upcoming 순, 최대 4개
        $events = Event::where('event_type', 'B')
            ->whereIn('status', ['active', 'upcoming'])
            ->orderByRaw("CASE status WHEN 'active' THEN 0 WHEN 'upcoming' THEN 1 ELSE 2 END")
            ->orderBy('start_date')
            ->limit(4)
            ->get();

        return view("home.{$activeTheme}", compact('activeTheme', 'events'));
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
