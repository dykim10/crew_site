<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * 이메일 인증 처리 컨트롤러 (Auth/VerifyEmailController.php)
 *
 * Laravel Breeze 자동 생성.
 * 인증 메일의 링크를 클릭했을 때 진입하며 email_verified_at 을 갱신한다.
 *
 * __invoke() GET /verify-email/{id}/{hash}
 *            미들웨어: signed (서명된 URL 검증) + throttle:6,1 (분당 6회 제한)
 *   → 이미 인증 완료된 경우: /dashboard?verified=1 리다이렉트
 *   → 미인증 상태: markEmailAsVerified() 호출 → email_verified_at 현재 시각으로 갱신
 *                  Verified 이벤트 발생 (리스너 연결 시 추가 처리 가능)
 *                  → /dashboard?verified=1 리다이렉트
 *
 * signed 미들웨어가 URL 의 서명·만료를 검증하므로 위변조된 링크는 403 으로 차단된다.
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->intended(route('dashboard', absolute: false).'?verified=1');
    }
}
