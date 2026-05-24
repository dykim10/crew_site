<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * 이메일 인증 안내 페이지 컨트롤러 (Auth/EmailVerificationPromptController.php)
 *
 * Laravel Breeze 자동 생성. verified 미들웨어가 미인증 사용자를 이 페이지로 리다이렉트한다.
 *
 * __invoke() GET /verify-email
 *   → 이미 인증 완료된 경우: /dashboard 리다이렉트
 *   → 미인증 상태: auth/verify-email 뷰 표시 (인증 메일 재발송 버튼 포함)
 *
 * __invoke() 단일 메서드만 가지는 단일 액션 컨트롤러(Single Action Controller).
 * 라우트에 클래스명만 등록하고 메서드명을 지정하지 않는 형태로 사용한다.
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
