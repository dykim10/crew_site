<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * 비밀번호 재확인 컨트롤러 (Auth/ConfirmablePasswordController.php)
 *
 * Laravel Breeze 자동 생성.
 * 계정 삭제 등 민감한 작업 전에 현재 비밀번호를 한 번 더 확인하는 용도.
 * password.confirm 미들웨어가 적용된 라우트 진입 시 자동으로 이 흐름으로 진입한다.
 *
 * show()  GET  /confirm-password → 비밀번호 입력 폼 표시
 * store() POST /confirm-password → 비밀번호 검증
 *                                   성공: 세션에 auth.password_confirmed_at 타임스탬프 저장
 *                                         → 원래 요청하던 페이지로 리다이렉트 (intended)
 *                                   실패: ValidationException → 비밀번호 오류 메시지 반환
 *
 * 세션에 저장된 confirmed_at 은 config('auth.password_timeout') 동안 유효하다 (기본 3시간).
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        if (! Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
