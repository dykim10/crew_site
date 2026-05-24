<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * 로그인 / 로그아웃 컨트롤러 (Auth/AuthenticatedSessionController.php)
 *
 * Laravel Breeze 자동 생성. 세션 기반 인증을 처리한다.
 *
 * create() GET  /login  → 로그인 폼 표시
 * store()  POST /login  → LoginRequest 로 인증 처리
 *                         세션 ID 재생성(세션 고정 공격 방지)
 *                         로그인 성공 시 last_login_at 갱신 후 /dashboard 리다이렉트
 *                         intended() 를 사용하므로 로그인 전에 접근했던 페이지로 자동 복귀
 * destroy() POST /logout → 세션 파기 + CSRF 토큰 재생성 후 / 리다이렉트
 */
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->update(['last_login_at' => now()]);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
