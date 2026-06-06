<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            // email 필드에는 email_hash 가 전달되므로 포맷 검사 제거
            'email'    => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // DB 에 email 컬럼이 없으므로 email_hash 로 사용자를 조회한다.
        $user = User::where('email_hash', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => __('passwords.user')]);
        }

        // 토큰 유효성 검사 (password_reset_tokens.email = email_hash 기준)
        $broker = Password::broker();
        if (!$broker->getRepository()->exists($user, $request->token)) {
            return back()->withErrors(['email' => __('passwords.token')]);
        }

        try {
            $user->forceFill([
                'password'       => Hash::make($request->password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));

            // 토큰 삭제
            $broker->getRepository()->delete($user);
        } catch (\Exception $e) {
            Log::error('비밀번호 재설정 저장 실패', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return back()->withErrors(['email' => '비밀번호 재설정에 실패했습니다. 다시 시도해주세요.']);
        }

        return redirect()->route('login')->with('status', __('passwords.reset'));
    }
}
