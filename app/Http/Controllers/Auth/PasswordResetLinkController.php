<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * 비밀번호 재설정 링크 요청 컨트롤러 (Auth/PasswordResetLinkController.php)
 *
 * Laravel Breeze 자동 생성. 비밀번호를 잊어버린 사용자에게 재설정 이메일을 발송한다.
 *
 * create() GET  /forgot-password → 이메일 입력 폼 표시
 * store()  POST /forgot-password → 이메일 검증 후 Password::sendResetLink() 호출
 *                                   발송 성공: back() + status 플래시 메시지
 *                                   발송 실패: back() + email 에러 메시지
 *
 * 실제 토큰 생성·저장·메일 발송은 Laravel 내장 Password 파사드가 처리한다.
 * 비밀번호 재설정 후 새 비밀번호 저장은 NewPasswordController 가 담당한다.
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
