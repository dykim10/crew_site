<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * 비밀번호 재설정 컨트롤러 (Auth/NewPasswordController.php)
 *
 * Laravel Breeze 자동 생성.
 * 이메일로 발송된 재설정 링크(서명된 URL)를 통해 진입하며 새 비밀번호를 저장한다.
 *
 * create() GET  /reset-password/{token} → 새 비밀번호 입력 폼 (token·email 을 hidden 으로 포함)
 * store()  POST /reset-password         → token·email·password 검증 후 Password::reset() 호출
 *                                          성공: 비밀번호 해시 저장 + remember_token 재생성
 *                                               + PasswordReset 이벤트 발생 → /login 리다이렉트
 *                                          실패: back() + email 에러 메시지
 *
 * 토큰 유효성 검사(만료·일치 여부)는 Password 파사드 내부에서 처리한다.
 */
class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
