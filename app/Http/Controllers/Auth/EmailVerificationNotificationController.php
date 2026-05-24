<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * 이메일 인증 메일 재발송 컨트롤러 (Auth/EmailVerificationNotificationController.php)
 *
 * Laravel Breeze 자동 생성.
 * 인증 메일을 받지 못하거나 만료된 사용자가 재발송을 요청할 때 사용한다.
 *
 * store() POST /email/verification-notification (throttle: 분당 6회 제한)
 *   → 이미 인증 완료된 경우: /dashboard 리다이렉트
 *   → 미인증 상태: sendEmailVerificationNotification() 호출로 인증 메일 발송
 *                  back() + status='verification-link-sent' 플래시 메시지
 *
 * 실제 이메일 발송은 User 모델이 구현하는 MustVerifyEmail 인터페이스를 통해 처리된다.
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
